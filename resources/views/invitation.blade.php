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
                        <h1>{{ config('invitation.host') }}</h1>
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

                        <div class="rsvp-done" data-rsvp-done @if (! $ticketGuest) hidden @endif>
                            <p>
                                <strong data-done-name>{{ $ticketGuest?->full_name }}</strong>
                                Билет оформлен на ваше имя. На баре для вас — <span data-done-drink>{{ $ticketGuest?->drink->phrase() }}</span>.
                            </p>
                            <button type="button" class="rsvp-edit" data-rsvp-edit>Изменить ответ</button>
                        </div>
                    </form>
                </div>

                <aside class="ticket-stub" aria-label="Корешок билета">
                    <div><span class="stub-label">Билет</span><span class="stub-number" data-stub-number>{{ $ticketGuest?->ticket_number ?? '№ ————' }}</span></div>
                    <div><span class="stub-label">Гость</span><span @class(['stub-guest', 'is-empty' => ! $ticketGuest]) data-stub-guest>{{ $ticketGuest?->full_name ?? '— — —' }}</span></div>
                    <div><span class="stub-label">Бар</span><span class="stub-drink" data-stub-drink>{{ $ticketGuest ? $ticketGuest->drink->code().' · '.$ticketGuest->drink->label() : 'не выбран' }}</span></div>
                    <div class="stub-barcode" aria-hidden="true"></div>
                    <small>Действителен {{ config('invitation.date') }}</small>
                </aside>
            </article>

            <a class="chat-link" href="{{ config('invitation.chat_url') }}" target="_blank" rel="noopener" data-chat-link>
                Перейти в чат гостей
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"/></svg>
            </a>
            <p class="page-note">Адрес и детали вечера — в чате</p>
        </main>
    </body>
</html>
