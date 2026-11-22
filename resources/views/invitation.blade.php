<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#C8102E">
        <title>{{ config('invitation.host') }} — день рождения</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=Oswald:wght@400;500;600&display=swap">

        @vite(['resources/css/invitation.css', 'resources/js/invitation.js'])
    </head>
    <body>
        <main class="page">
            <article class="ticket">
                <div class="ticket-main">
                    <div class="ticket-top">
                        <span>Пригласительный билет</span>
                        <b>Вход по списку</b>
                    </div>

                    <div class="ticket-head">
                        <span class="ticket-kicker">День рождения</span>
                        <h1 class="flip-title" aria-label="{{ config('invitation.host') }}">@foreach (mb_str_split(config('invitation.host')) as $letter)<span class="flip-char" style="--i: {{ $loop->index }}" aria-hidden="true">{{ $letter }}</span>@endforeach</h1>
                    </div>

                    <dl class="ticket-data">
                        <div><dt>Дата</dt><dd>{{ config('invitation.date') }}</dd></div>
                        <div><dt>Начало</dt><dd>{{ config('invitation.time') }}</dd></div>
                        <div><dt>Место</dt><dd>{{ config('invitation.place') }}</dd></div>
                    </dl>

                    <h2 class="ticket-section">Регистрация гостя</h2>

                    <form class="rsvp" id="rsvp" method="POST" action="{{ route('guests.store') }}" novalidate>
                        @csrf
                        <div class="rsvp-fields" data-rsvp-fields @if ($ticketGuest) hidden @endif>
                            <div class="name-row">
                                <div class="field">
                                    <label for="guest-first-name">Имя</label>
                                    <input id="guest-first-name" name="first_name" value="{{ old('first_name') }}" maxlength="60" autocomplete="given-name" placeholder="Имя" @error('first_name') aria-invalid="true" @enderror>
                                </div>
                                <div class="field">
                                    <label for="guest-last-name">Фамилия</label>
                                    <input id="guest-last-name" name="last_name" value="{{ old('last_name') }}" maxlength="60" autocomplete="family-name" placeholder="Фамилия" @error('last_name') aria-invalid="true" @enderror>
                                </div>
                            </div>

                            <fieldset>
                                <legend>Напиток на баре</legend>
                                <div class="drinks">
                                    @foreach ($drinks as $drink)
                                        <label class="drink">
                                            <input type="radio" name="drink" id="drink-{{ $drink->value }}" value="{{ $drink->value }}" @checked(old('drink') === $drink->value)>
                                            <span class="drink-box">
                                                <span class="drink-code">{{ $drink->code() }}</span>
                                                <span class="drink-name">{{ $drink->label() }}</span>
                                                <x-drink-icon :type="$drink->icon()" />
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>

                            <p class="rsvp-error" role="alert" data-rsvp-error @if (! $errors->any()) hidden @endif>{{ $errors->first() }}</p>
                            <button class="rsvp-submit" type="submit">Получить билет</button>
                        </div>

                        <div class="coupon" data-rsvp-done @if (! $ticketGuest) hidden @endif>
                            <div class="coupon-top">
                                <span>Контрольный талон</span>
                                <span data-done-number>{{ $ticketGuest?->ticket_number }}</span>
                            </div>
                            <strong class="coupon-name flip-in" data-done-name>{{ $ticketGuest?->full_name }}</strong>
                            <p class="coupon-text">Билет оформлен на ваше имя. На баре для вас — <span data-done-drink>{{ $ticketGuest?->drink->phrase() }}</span>.</p>
                            <div class="coupon-bottom">
                                <div class="punch-grid" data-punch @if ($ticketGuest) data-punched="{{ $ticketGuest->ticket_number }}" @endif aria-hidden="true"></div>
                                <button type="button" class="rsvp-edit" data-rsvp-edit>Изменить ответ</button>
                            </div>
                            <div class="coupon-stamp" data-coupon-stamp aria-hidden="true">
                                <span>Вы в списке</span>
                                <small>{{ config('invitation.date') }}</small>
                            </div>
                        </div>
                    </form>
                </div>

                <aside class="ticket-stub" aria-label="Корешок билета">
                    <div><span class="stub-label">Билет</span><span class="stub-number" data-stub-number>{{ $ticketGuest?->ticket_number ?? '№ ————' }}</span></div>
                    <div><span class="stub-label">Гость</span><span @class(['stub-guest', 'flip-in', 'is-empty' => ! $ticketGuest]) data-stub-guest>{{ $ticketGuest?->full_name ?? '— — —' }}</span></div>
                    <div><span class="stub-label">Бар</span><span class="stub-drink" data-stub-drink>{{ $ticketGuest ? $ticketGuest->drink->code().' · '.$ticketGuest->drink->label() : 'не выбран' }}</span></div>
                    <div class="stub-barcode" aria-hidden="true"></div>
                    <div @class(['stub-stamp', 'is-confirmed' => $ticketGuest]) data-stub-stamp aria-hidden="true">
                        <span data-stamp-text>{{ $ticketGuest ? 'Подтверждено' : 'Приглашение' }}</span>
                        <small>{{ config('invitation.date') }}</small>
                    </div>
                    <small>Действителен {{ config('invitation.date') }}</small>
                </aside>
            </article>

            <a class="chat-link" href="{{ config('invitation.chat_url') }}" target="_blank" rel="noopener" data-chat-link>
                Перейти в чат гостей
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"/></svg>
            </a>
            <p class="page-note">Адрес и детали вечера — в чате</p>
            <footer class="page-credit">Created by Sergey Druzhinin</footer>
        </main>
    </body>
</html>
