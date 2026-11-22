<x-host-layout title="Список гостей">
    <section class="sheet">
        <div class="sheet-top">
            <span>{{ config('invitation.host') }} · {{ config('invitation.date') }}</span>
            <form method="POST" action="{{ route('host.logout') }}">
                @csrf
                <button type="submit" class="link-btn">Выйти</button>
            </form>
        </div>

        <div class="list-head">
            <h1>Список гостей</h1>
            <p class="total"><b>{{ $guests->count() }}</b> {{ $guestCountLabel }}</p>
        </div>

        @if (session('status'))
            <p class="status" role="status">{{ session('status') }}</p>
        @endif

        <div class="bar">
            <h2>На бар</h2>
            <ul class="bar-grid">
                @foreach ($drinks as $drink)
                    <li @class(['bar-item', 'is-zero' => $drinkCounts[$drink->value] === 0])>
                        <span class="bar-code">{{ $drink->code() }}</span>
                        <x-drink-icon :type="$drink->icon()" />
                        <span class="bar-name">{{ $drink->label() }}</span>
                        <span class="bar-count">{{ $drinkCounts[$drink->value] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <h2 class="section-title">Гости</h2>

        @if ($guests->isEmpty())
            <div class="empty">
                <p><b>Пока никто не ответил.</b></p>
                <p>Ответы появятся здесь, как только гости заполнят форму на <a href="{{ route('invitation') }}">приглашении</a>.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="guests">
                    <thead>
                        <tr>
                            <th scope="col">Билет</th>
                            <th scope="col">Гость</th>
                            <th scope="col">Напиток</th>
                            <th scope="col">Ответ</th>
                            <th scope="col"><span class="sr">Действия</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($guests as $guest)
                            <tr>
                                <td class="num">{{ $guest->ticket_number }}</td>
                                <td class="name">{{ $guest->full_name }}</td>
                                <td><span class="drink-tag">{{ $guest->drink->code() }}</span> {{ $guest->drink->label() }}</td>
                                <td class="when">{{ $guest->updated_at->timezone(config('invitation.timezone'))->format('d.m H:i') }}</td>
                                <td class="actions">
                                    <form method="POST" action="{{ route('guests.destroy', $guest) }}"
                                        onsubmit="return confirm({{ Js::from('Удалить '.$guest->full_name.' из списка?') }})">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="link-btn link-btn--danger">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-host-layout>
