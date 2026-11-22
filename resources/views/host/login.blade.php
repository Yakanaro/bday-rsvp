<x-host-layout title="Вход" class="host-page host-page--narrow">
    <section class="sheet login">
        <div class="sheet-top">
            <span>Служебный вход</span>
            <b>Только для хозяина</b>
        </div>

        <h1>Список гостей</h1>
        <p class="login-lead">Введите пароль, чтобы посмотреть, кто придёт и что налить.</p>

        <form method="POST" action="{{ route('host.login.store') }}" class="login-form" novalidate>
            @csrf
            <div class="field">
                <label for="host-password">Пароль</label>
                <input id="host-password" name="password" type="password" autocomplete="current-password" autofocus @error('password') aria-invalid="true" @enderror>
            </div>

            @error('password')
                <p class="form-error" role="alert">{{ $message }}</p>
            @enderror

            <button type="submit" class="btn btn--dark">Открыть список</button>
        </form>

        <a class="back-link" href="{{ route('invitation') }}">← К приглашению</a>
    </section>
</x-host-layout>
