<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Exceptions\RoomUnavailableException;
use Carbon\Carbon;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseFormatter;
use App\Http\Requests\ReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\ReservationDetailsResponse;
use App\Models\ReservationRoomDetails;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

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
            $reservation = Reservation::findOrFail($id);
            return $this->success($reservation->load('reservationDetails','addons'));
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
            return $this->success($updatedReservation->load('reservationDetails','addons'), 'Reservation updated successfully!', 200);
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
            return $this->success($reservation->load('reservationDetails','addons'), 'Reservation deleted successfully!');
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
                    ->first();
                $reservation->delete();
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

    public function checkout(Request $request, Reservation $reservation)
    {
        dd($reservation);
    }
}
