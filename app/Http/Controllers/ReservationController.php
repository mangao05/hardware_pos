<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Room;
use App\Models\Leisure;
use App\Models\Reservation;
use App\Models\RoomCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ReservationPayments;
use App\Http\Traits\ResponseFormatter;
use App\Models\ReservationRoomDetails;
use App\Http\Requests\ReservationRequest;
use App\Exceptions\RoomUnavailableException;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\ReservationDetailsResponse;

class ReservationController extends Controller
{
    use ResponseFormatter;

    public function index(Request $request)
    {
        try {

            $query = ReservationRoomDetails::query();

            if ($request->has('month') && $request->has('year')) {
                $startOfMonth = Carbon::create($request->year, $request->month, 1)->startOfMonth();
                $endOfMonth = Carbon::create($request->year, $request->month, 1)->endOfMonth();

                $query->where(function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->where('check_in_date', '<=', $endOfMonth)
                        ->where('check_out_date', '>=', $startOfMonth);
                });
            }

            $reservations = $query->orderBy('check_in_date')->get();

            return $this->success(ReservationDetailsResponse::collection($reservations));
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $reservation = Reservation::with(['reservationDetails', 'addons', 'payments'])->findOrFail($id);

            $categoryMap = RoomCategory::all()->pluck('display_name', 'id');

            foreach ($reservation->reservationDetails as $detail) {
                $roomDetails = $detail->room_details;

                if (isset($roomDetails['room_category_id'])) {
                    $roomCategoryId = $roomDetails['room_category_id'];
                    $roomCategoryName = $categoryMap[$roomCategoryId] ?? '';
                    $roomDetails['room_category_name'] = $roomCategoryName;
                } else {
                    $roomDetails['room_category_name'] = '';
                }

                $detail->room_details = $roomDetails;
            }

            return $this->success($reservation);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function store(ReservationRequest $request)
    {
        try {
            DB::beginTransaction();
            $reservation = Reservation::createWithDetails($request->all());
            DB::commit();
            return $this->success($reservation->load('reservationDetails', 'addons'), 'Reservation created successfully!', 201);
        } catch (RoomUnavailableException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'unavailable_rooms' => $e->getUnavailableRooms()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        try {
            DB::beginTransaction();

            $updatedReservation = $reservation->updateWithDetails($reservation, $request->all());

            DB::commit();
            return $this->success($updatedReservation->load('reservationDetails', 'addons'), 'Reservation updated successfully!', 200);
        } catch (RoomUnavailableException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'unavailable_rooms' => $e->getUnavailableRooms()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $reservation = Reservation::findOrFail($id);
            $logs = [
                'action' => 'deleted',
                'message' => 'Deleted Room Reservation',
                'reservation_id' => $reservation->id,
                'old_data' => $reservation->load('reservationDetails')
            ];
            $reservation->logAction($logs);
            $reservation->delete();
            DB::commit();
            return $this->success($reservation->load('reservationDetails', 'addons'), 'Reservation deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function updateReservationRoomStatus(Request $request, $reservation, $room)
    {
        try {
            DB::beginTransaction();
            $roomReservation = ReservationRoomDetails::where('reservation_id', $reservation)
                ->where('room_id', $room)
                ->first();

            $oldData = $roomReservation->getOriginal();
            $roomReservation->update([
                'status' => $request->status
            ]);

            $logs = [
                'action' => $request->status,
                'message' => 'Updated Room Reservation Status',
                'reservation_id' => $reservation,
                'room_id' => $room,
                'new_data' => $roomReservation,
                'old_data' => $oldData
            ];

            $roomReservation->logAction($logs);

            DB::commit();
            return $this->success($roomReservation->load(['room', 'reservation']), 'Reservation room status updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function changeReservationRoom(Request $request, Reservation $reservation)
    {
        try {
            DB::beginTransaction();
            $unavailableRooms = Reservation::checkRoomAvailability($request->new_room, $request->check_in_date, $request->check_out_date);

            if (!empty($unavailableRooms)) {
                throw new RoomUnavailableException($unavailableRooms, 'The following rooms are unavailable.');
            }
            if ($request->has('old_room')) {
                ReservationRoomDetails::where('reservation_id', $reservation->id)
                    ->where('room_id', $request->old_room)
                    ->first()
                    ->delete();
            }

            $reservation->addReservationDetails($request->new_room, $request->check_in_date, $request->check_out_date);

            $logs = [
                'action' => $request->has('old_room') ? 'transfer_room' : 'add_room',
                'message' => 'Updated Room Reservation',
                'reservation_id' => $reservation->id,
                'new_data' => $reservation->load('reservationDetails'),
                'old_data' => $reservation->getOriginal()
            ];

            $reservation->logAction($logs);

            $message = $request->has('old_room') ? 'Reservation room changed successfully!' : 'Reservation room added successfully!';

            DB::commit();
            return $this->success($reservation->load('reservationDetails'), $message);
        } catch (RoomUnavailableException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'unavailable_rooms' => $e->getUnavailableRooms()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function checkout(Request $request)
    {
        DB::beginTransaction();
        try {
            $total_amount = (float) $request->total;
            $payment = (float) $request->initial_payment;

            if (! empty($request->reservation_id)) {
                $reservation = Reservation::find($request->reservation_id);

                // Retrieve the most recent payment entry
                $lastPayment = $reservation->payments()->orderBy('created_at', 'desc')->first();

                $initial_balance = $total_amount - $payment;
                $current_balance = $initial_balance;

                if (!empty($lastPayment)) {
                    $previous_total_amount = (float) $reservation->total_amount; // Assuming `total_amount` is stored on the reservation
                    $balance_change = $total_amount - $previous_total_amount;

                    $current_balance = $lastPayment->balance + $balance_change - $payment;
                }

                $data = [
                    'customer' => $request->customer_name,
                    'user_id' => auth()->check() ? auth()->user()->id : null,
                    'user_name' => auth()->check() ? auth()->user()->firstname . ' ' . auth()->user()->lastname : 'Guest',
                    'initial_payment' => $payment,
                    'balance' => $current_balance,
                    'transaction_number' => $request->transaction_number,
                    'payment_method' => $request->payment_method,
                    'payment_type' => $request->payment_method['type_payment']
                ];

                $reservation->payments()->create($data);
                $reservation->update([
                    'discount' => $request->discount,
                    'total_amount' => $total_amount,
                    'payment_status' => $current_balance > 0 ? 'unpaid' : 'paid'
                ]);
                DB::commit();
                return $this->success($reservation->load('payments'), "Payment for Reservation success");
            } else {
                $payment = ReservationPayments::create([
                    'customer' => $request->customer_name,
                    'user_id' => auth()->id(),
                    'user_name' => auth()->check() ? auth()->user()->firstname . ' ' . auth()->user()->lastname : 'Guest',
                    'initial_payment' => $payment,
                    'balance' => 0,
                    'transaction_number' => $request->transaction_number
                ]);

                if (empty($request->addons)) {
                    throw new Exception('No add-ons added.');
                }

                $addonIds = collect($request->addons)->pluck('addon_id')->toArray();
                $leisures = Leisure::whereIn('id', $addonIds)->get()->keyBy('id');

                $addonData = collect($request->addons)->map(function ($addon) use ($leisures) {
                    $leisure = $leisures[$addon['addon_id']] ?? null;

                    return [
                        'addon_id' => $addon['addon_id'],
                        'addon_price' => $addon['addon_price'],
                        'addon_name' => $leisure ? $leisure->item_name : null,
                        'addon_details' => $leisure,
                        'qty' => $addon['qty']
                    ];
                })->toArray();

                $payment->addons()->createMany($addonData);

                DB::commit();
                return $this->success($payment->load('addons'), "Walk in payment success.");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function voidPayment()
    {
        try {
            $paymentId = request()->get('reservation_payment_id');
            $payment = ReservationPayments::findOrFail($paymentId);
            $payment->delete();

            return $this->success($payment, 'Payment voided successfully.');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }
}
