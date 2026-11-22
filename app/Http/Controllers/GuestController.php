<?php

namespace App\Http\Controllers;

use App\Drink;
use App\Http\Requests\StoreGuestRequest;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GuestController extends Controller
{
    /**
     * Show the host who is coming and how many of each drink to buy.
     */
    public function index(): View
    {
        $guests = Guest::query()->orderBy('id')->get();

        $drinkCounts = collect(Drink::cases())
            ->mapWithKeys(fn (Drink $drink): array => [
                $drink->value => $guests->where('drink', $drink)->count(),
            ]);

        return view('guests.index', [
            'guests' => $guests,
            'guestCountLabel' => $this->guestCountLabel($guests->count()),
            'drinks' => Drink::cases(),
            'drinkCounts' => $drinkCounts,
        ]);
    }

    /**
     * The Russian word for "guest" that agrees with the number: 1 гость, 3 гостя, 5 гостей, 21 гость.
     */
    private function guestCountLabel(int $count): string
    {
        $lastTwoDigits = $count % 100;
        $lastDigit = $count % 10;

        return match (true) {
            $lastTwoDigits >= 11 && $lastTwoDigits <= 14 => 'гостей',
            $lastDigit === 1 => 'гость',
            $lastDigit >= 2 && $lastDigit <= 4 => 'гостя',
            default => 'гостей',
        };
    }

    /**
     * Sign a guest up and issue their ticket. Signing up again under the same
     * name hands back the same ticket, with the drink updated to the new choice.
     */
    public function store(StoreGuestRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $guest = Guest::query()->createOrFirst(
            ['name_key' => Guest::nameKey($validated['first_name'], $validated['last_name'])],
            $validated,
        );

        if (! $guest->wasRecentlyCreated) {
            $guest->update(['drink' => $validated['drink']]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ticket' => [
                    'number' => $guest->ticket_number,
                    'name' => $guest->full_name,
                    'drink' => $guest->drink->label(),
                    'drink_code' => $guest->drink->code(),
                    'drink_phrase' => $guest->drink->phrase(),
                ],
            ], $guest->wasRecentlyCreated ? 201 : 200);
        }

        return redirect()
            ->to(route('invitation').'#rsvp')
            ->with('ticket_guest_id', $guest->id);
    }

    /**
     * Remove a guest from the list.
     */
    public function destroy(Guest $guest): RedirectResponse
    {
        $guest->delete();

        return redirect()
            ->route('guests.index')
            ->with('status', "{$guest->full_name} удалён(а) из списка.");
    }
}
