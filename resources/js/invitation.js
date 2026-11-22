/**
 * Guest RSVP on the invitation ticket: checks the form, sends it to the
 * server and fills the ticket stub with the ticket the server issues.
 * Without JavaScript the form still posts normally and the page comes back
 * with the ticket filled in.
 */
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const restartAnimation = (element, className) => {
    element.classList.remove(className);
    void element.offsetWidth;
    element.classList.add(className);
};

const form = document.getElementById('rsvp');

if (form) {
    const fields = form.querySelector('[data-rsvp-fields]');
    const done = form.querySelector('[data-rsvp-done]');
    const error = form.querySelector('[data-rsvp-error]');
    const submit = form.querySelector('.rsvp-submit');
    const firstNameInput = form.elements.first_name;
    const lastNameInput = form.elements.last_name;
    const stubNumber = document.querySelector('[data-stub-number]');
    const stubGuest = document.querySelector('[data-stub-guest]');
    const stubDrink = document.querySelector('[data-stub-drink]');
    const chatLink = document.querySelector('[data-chat-link]');
    const stamp = document.querySelector('[data-stub-stamp]');

    const flipText = (element, text) => {
        element.setAttribute('aria-label', text);
        element.replaceChildren(...[...text].map((letter, index) => {
            const span = document.createElement('span');
            span.className = 'flip-char';
            span.style.setProperty('--i', index);
            span.setAttribute('aria-hidden', 'true');
            span.textContent = letter;

            return span;
        }));
    };

    const rollNumber = (element, finalText) => {
        if (prefersReducedMotion) {
            element.textContent = finalText;

            return;
        }

        const startedAt = performance.now();
        const duration = 700;
        const tick = (now) => {
            const progress = Math.min((now - startedAt) / duration, 1);
            const settledDigits = Math.floor(progress * finalText.length);
            element.textContent = [...finalText]
                .map((character, index) => (index < settledDigits || !/\d/.test(character)
                    ? character
                    : String(Math.floor(Math.random() * 10))))
                .join('');

            if (progress < 1) {
                requestAnimationFrame(tick);
            }
        };
        requestAnimationFrame(tick);
    };

    const markInvalid = (input, isInvalid) => {
        if (isInvalid) {
            input.setAttribute('aria-invalid', 'true');
        } else {
            input.removeAttribute('aria-invalid');
        }
    };

    const showError = (message) => {
        error.textContent = message;
        error.hidden = false;
    };

    const showTicket = (ticket) => {
        error.hidden = true;
        done.querySelector('[data-done-name]').textContent = ticket.name;
        done.querySelector('[data-done-drink]').textContent = ticket.drink_phrase;
        fields.hidden = true;
        done.hidden = false;

        rollNumber(stubNumber, ticket.number);
        flipText(stubGuest, ticket.name);
        stubGuest.classList.remove('is-empty');
        stubDrink.textContent = `${ticket.drink_code} · ${ticket.drink}`;

        stamp.querySelector('[data-stamp-text]').textContent = 'Подтверждено';
        stamp.classList.add('is-confirmed');
        restartAnimation(stamp, 'is-restamped');
        restartAnimation(chatLink, 'is-ready');
    };

    [firstNameInput, lastNameInput].forEach((input) => {
        input.addEventListener('input', () => markInvalid(input, false));
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const firstName = firstNameInput.value.trim();
        const lastName = lastNameInput.value.trim();
        const pickedDrink = form.querySelector('input[name="drink"]:checked');

        markInvalid(firstNameInput, !firstName);
        markInvalid(lastNameInput, !lastName);

        const problems = [];
        if (!firstName || !lastName) {
            problems.push('укажите имя и фамилию');
        }
        if (!pickedDrink) {
            problems.push('выберите напиток');
        }

        if (problems.length) {
            const message = problems.join(' и ');
            showError(`${message.charAt(0).toUpperCase()}${message.slice(1)}.`);
            (!firstName ? firstNameInput : !lastName ? lastNameInput : form.querySelector('input[name="drink"]')).focus();

            return;
        }

        error.hidden = true;
        submit.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: new FormData(form),
            });
            const body = await response.json().catch(() => ({}));

            if (response.status === 422) {
                const errors = body.errors ?? {};
                markInvalid(firstNameInput, Boolean(errors.first_name));
                markInvalid(lastNameInput, Boolean(errors.last_name));
                showError(Object.values(errors)[0]?.[0] ?? 'Проверьте поля формы.');

                return;
            }

            if (!response.ok) {
                const messages = {
                    419: 'Страница устарела. Обновите её и отправьте ещё раз.',
                    429: 'Слишком много попыток. Подождите минуту и попробуйте снова.',
                };
                showError(messages[response.status] ?? 'Не получилось отправить. Обновите страницу и попробуйте ещё раз.');

                return;
            }

            showTicket(body.ticket);
        } catch {
            showError('Нет связи с сервером. Проверьте интернет и попробуйте ещё раз.');
        } finally {
            submit.disabled = false;
        }
    });

    form.querySelector('[data-rsvp-edit]').addEventListener('click', () => {
        done.hidden = true;
        fields.hidden = false;
        firstNameInput.focus();
    });
}
