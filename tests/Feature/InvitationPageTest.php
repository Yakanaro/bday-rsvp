<?php

namespace Tests\Feature;

use App\Drink;
use Tests\TestCase;

class InvitationPageTest extends TestCase
{
    public function test_home_page_shows_the_invitation_ticket(): void
    {
        $response = $this->get(route('invitation'));

        $response->assertOk();
        $response->assertSee('Евгений');
        $response->assertSee('Регистрация гостя');
        $response->assertSee('name="first_name"', false);
        $response->assertSee('name="last_name"', false);
    }

    public function test_guest_can_choose_each_configured_drink(): void
    {
        $response = $this->get(route('invitation'));

        $response->assertSeeInOrder(['Виски', 'Водка', 'Вино белое', 'Вино красное', 'Не пью']);

        foreach (Drink::cases() as $drink) {
            $response->assertSee('value="'.$drink->value.'"', false);
        }
    }

    public function test_ticket_shows_the_event_time_and_venue(): void
    {
        $response = $this->get(route('invitation'));

        $response->assertSeeInOrder(['Начало', '18:00', 'Место', 'Ресторан-пивоварня', 'пл. Гагарина, 1']);
    }

    public function test_route_map_pins_the_venue(): void
    {
        config([
            'invitation.city' => 'Петрозаводск',
            'invitation.address' => 'пл. Гагарина, 1',
            'invitation.coordinates' => ['lat' => 61.784308, 'lon' => 34.346825],
        ]);

        $response = $this->get(route('invitation'));

        $response->assertSee('Схема проезда');
        $response->assertSee('https://yandex.ru/map-widget/v1/?ll=34.346825%2C61.784308&amp;z=17&amp;pt=34.346825%2C61.784308%2Cpm2rdl', false);
        $response->assertSee('https://2gis.ru/search/'.rawurlencode('Петрозаводск, пл. Гагарина, 1'), false);
    }

    public function test_chat_button_uses_the_configured_chat_url(): void
    {
        config(['invitation.chat_url' => 'https://t.me/+evgeny-guests']);

        $response = $this->get(route('invitation'));

        $response->assertSee('href="https://t.me/+evgeny-guests"', false);
        $response->assertSee('Перейти в чат гостей');
    }
}
