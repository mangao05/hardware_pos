<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ReservationPayments;
use App\Http\Traits\ResponseFormatter;
use App\Models\ReservationRoomDetails;

class ReportsController extends Controller
{
    use ResponseFormatter;
    public function room_statuses()
    {
        $roomsByCategory = Room::with('category:id,display_name,availability')
            ->orderBy('room_category_id')
            ->get()
            ->groupBy(fn($room) => $room->category->display_name)
            ->map(fn($rooms) => [
                'all_rooms' => $rooms->pluck('id')->toArray(),
                'out_of_service' => $rooms->where('availability', false)->pluck('id')->toArray()
            ]);

        $today = request()->get('date', now()->toDateString());

        $occupiedRoomIds = ReservationRoomDetails::whereDate('check_in_date', '<=', $today)
            ->whereDate('check_out_date', '>=', $today)
            ->where('status', 'checkin')
            ->pluck('room_id')
            ->toArray();

        $bookedRoomIds = ReservationRoomDetails::where('status', 'booked')
            ->pluck('room_id')
            ->toArray();

        $statusCounts = [];

        foreach ($roomsByCategory as $categoryName => $roomData) {
            $allRooms = $roomData['all_rooms'];
            $outOfServiceRooms = $roomData['out_of_service'];

            $availableRooms = array_diff($allRooms, $occupiedRoomIds, $outOfServiceRooms);
            $occupiedRooms = array_intersect($allRooms, $occupiedRoomIds);
            $bookedRooms = array_intersect($allRooms, $bookedRoomIds);

            $statusCounts[] = [
                'category_name' => $categoryName,
                'total' => count($allRooms),
                'available' => count($availableRooms),
                'occupied' => count($occupiedRooms),
                'out_of_service' => count($outOfServiceRooms),
                'reserved' => count($bookedRooms)
            ];
        }

        return $this->success([
            "sales_summary" => $this->sales_summary($today),
            "room_statuses" =>  $statusCounts
        ]);
    }


    private function sales_summary($date)
    {
        $payments = ReservationPayments::select('initial_payment', 'user_name', 'user_id')
            ->when(request()->filled('from') && request()->filled('to'), function ($query) {
                $startDate = Carbon::createFromFormat('m-d-Y', request()->get('from'));
                $endDate = Carbon::createFromFormat('m-d-Y', request()->get('to'));
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when(request()->filled('year') && !request()->filled('from') && !request()->filled('to'), function ($query) {
                return $query->whereYear('created_at', request()->get('year'));
            })
            ->when(!request()->filled('year') && !request()->filled('from') && !request()->filled('to'), function ($query) use ($date){
                return $query->whereDate('created_at', $date);
            })
            ->get();

        $userIdWithSales = array_unique($payments->pluck('user_id')->toArray());
        $users = User::whereHas('roles', function ($query) {
            return $query->whereIn('role_id', [2]);
        })->whereNotIn('id', $userIdWithSales)
            ->get();

        $reports = [];

        foreach ($payments as $payment) {
            if (isset($reports[$payment->user_name])) {
                $reports[$payment->user_name] += $payment->initial_payment;
            } else {
                $reports[$payment->user_name] = (double)$payment->initial_payment;
            }
        }
        
        foreach ($users as $user) {
            $reports[$user->firstname . ' ' . $user->lastname] = 0;
        }

        $finalReports = [];
        foreach ($reports as $userName => $totalPayment) {
            $finalReports[] = [
                'user_name' => $userName,
                'sales' => $totalPayment,
            ];
        }

        return $finalReports;
    }

    public function payments_summary()
    {
        try {
            $date = request()->get('date', now());
            $payments = ReservationPayments::whereDate('created_at', $date)
                ->whereNull('reservation_id')
                ->paginate(10);
            return $this->success($payments, 'Successfully fetched payments.');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        };
    }

    public function getRoomBookings(Request $request)
    {

        $startDate = $request->from ? Carbon::createFromFormat('m-d-Y', $request->from) : Carbon::now()->startOfMonth();
        $endDate = $request->to ? Carbon::createFromFormat('m-d-Y', $request->to) : Carbon::now()->endOfMonth();

        $bookings = ReservationRoomDetails::when($request->type === 'range', function ($query)  use ($startDate, $endDate) {
            return $query->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('check_in_date', [$startDate, $endDate])
                    ->whereBetween('check_out_date', [$startDate, $endDate]);
            });
        })->when($request->type === 'year', function ($query) use ($request) {
            $year = $request->year ?? now()->year;

            return $query->whereYear('check_in_date', $year)
                ->whereYear('check_out_date', $year);
        })->when($request->room_category_id, function ($query) use ($request) {
            return $query->whereHas('room', function ($query) use ($request) {
                $query->where('room_category_id', $request->room_category_id);
            });
        })->orderBy('room_id')->get();

        $total_bookings = [];

        foreach ($bookings as $booking) {
            if (! isset($total_bookings[$booking->room_id])) {
                $total_bookings[$booking->room_id] = [
                    'room_id' => $booking->room_id,
                    'room_name' => $booking->room->name,
                    'total_bookings' => 1,
                    'reservation_id' => $booking->reservation_id
                ];
            } else {
                $total_bookings[$booking->room_id]['total_bookings'] += 1;
            }
            $total_bookings[$booking->room_id]['total_sales'] = 0;
        }
        // Group and calculate total bookings & total sales per room
        // $bookings = $bookings
        //     ->selectRaw('
        //     room_id, 
        //     COUNT(*) as total_bookings, 
        //     SUM(DATEDIFF(check_out_date, check_in_date) * JSON_UNQUOTE(JSON_EXTRACT(room_details, "$.price"))) as total_sales
        // ')
        //     ->groupBy('room_id')
        //     ->orderByDesc('total_sales')
        //     ->get();

        return response()->json(array_values($total_bookings));
    }
}
