<?php

namespace App\Http\Controllers;

use App\Drink;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationController extends Controller
{
    /**
     * Show the invitation ticket. After a sign-up without JavaScript the
     * guest's ticket comes back already filled in.
     */
    public function __invoke(Request $request): View
    {
        $ticketGuestId = $request->session()->get('ticket_guest_id');

        return view('invitation', [
            'drinks' => Drink::cases(),
            'ticketGuest' => $ticketGuestId ? Guest::find($ticketGuestId) : null,
            'venueMap' => $this->venueMap(),
        ]);
    }

    /**
     * Links for the route map: the embedded Yandex map with a pin on the venue,
     * the same point in Yandex Maps and the venue address searched in 2GIS.
     *
     * @return array{embed: string, yandex: string, twoGis: string}
     */
    private function venueMap(): array
    {
        $venuePoint = config('invitation.coordinates.lon').','.config('invitation.coordinates.lat');
        $venueAddress = config('invitation.city').', '.config('invitation.address');

        return [
            'embed' => 'https://yandex.ru/map-widget/v1/?'.http_build_query(['ll' => $venuePoint, 'z' => 17, 'pt' => $venuePoint.',pm2rdl']),
            'yandex' => 'https://yandex.ru/maps/?'.http_build_query(['ll' => $venuePoint, 'z' => 17, 'pt' => $venuePoint.',pm2rdl']),
            'twoGis' => 'https://2gis.ru/search/'.rawurlencode($venueAddress),
        ];
    }
}
