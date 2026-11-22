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

const shake = (element) => element.animate([
    { transform: 'none' },
    { transform: 'translateY(3px) scale(.994)' },
    { transform: 'none' },
], { duration: 220, easing: 'ease-out' });

/**
 * Ticket punch on the control coupon. Each guest gets their own hole pattern,
 * worked out from the ticket number: four rows of three, one row per digit.
 */
const PUNCH_COLUMNS = 3;
const PUNCH_ROWS = 4;

const punchPattern = (ticketNumber) => {
    const digits = ticketNumber.replace(/\D/g, '').padStart(PUNCH_ROWS, '0').slice(-PUNCH_ROWS);
    const ticketId = Number(digits);

    return [...digits].flatMap((digit, row) => {
        const bits = (Number(digit) * 5 + row * 3 + ticketId * (row + 2) + 7) % 8 || 0b010;

        return Array.from({ length: PUNCH_COLUMNS }, (_, column) => Boolean(bits & (1 << column)));
    });
};

const dropChad = (layer, hole, delay) => {
    const layerBox = layer.getBoundingClientRect();
    const holeBox = hole.getBoundingClientRect();
    const chad = document.createElement('span');
    chad.className = 'punch-chad';
    chad.style.left = `${holeBox.left - layerBox.left}px`;
    chad.style.top = `${holeBox.top - layerBox.top}px`;
    layer.append(chad);

    const driftX = (Math.random() - 0.5) * 70;
    const fallY = 110 + Math.random() * 90;
    const spin = (Math.random() - 0.5) * 540;

    chad.animate([
        { transform: 'translate(0, 0) rotate(0deg)', opacity: 1 },
        { transform: `translate(${driftX * 0.35}px, -22px) rotate(${spin * 0.3}deg)`, opacity: 1, offset: 0.22 },
        { transform: `translate(${driftX}px, ${fallY}px) rotate(${spin}deg)`, opacity: 0 },
    ], {
        duration: 900 + Math.random() * 500,
        delay,
        easing: 'cubic-bezier(.35, .1, .7, 1)',
        fill: 'both',
    }).finished.then(() => chad.remove());
};

const punchTicket = (punch, ticketNumber, isAnimated) => {
    const chadLayer = punch.closest('.rsvp');

    punch.replaceChildren(...punchPattern(ticketNumber).map((isHole, index) => {
        const cell = document.createElement('span');
        cell.className = isHole ? 'punch-hole is-hole' : 'punch-hole';
        cell.style.setProperty('--i', index);

        return cell;
    }));

    if (!isAnimated) {
        return;
    }

    punch.querySelectorAll('.is-hole').forEach((hole) => {
        const delay = Number(hole.style.getPropertyValue('--i')) * 35;
        hole.classList.add('is-new');
        dropChad(chadLayer, hole, delay);
    });
};

const couponPunch = document.querySelector('[data-punch]');

if (couponPunch?.dataset.punched) {
    punchTicket(couponPunch, couponPunch.dataset.punched, false);
}

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

    /**
     * Rebuild text as letters that flip in one by one. Letters are grouped
     * into words so spaces survive and long names wrap between words.
     */
    const flipText = (element, text) => {
        let letterIndex = 0;
        element.setAttribute('aria-label', text);
        const parts = text.split(/(\s+)/).filter(Boolean)
            .flatMap((part) => (/^\s+$/.test(part) ? [part] : part.split(/(?<=-)/)));

        element.replaceChildren(...parts.map((part) => {
            if (/^\s+$/.test(part)) {
                return document.createTextNode(' ');
            }

            const word = document.createElement('span');
            word.className = 'flip-word';
            word.setAttribute('aria-hidden', 'true');
            word.append(...[...part].map((letter) => {
                const span = document.createElement('span');
                span.className = 'flip-char';
                span.style.setProperty('--i', letterIndex++);
                span.textContent = letter;

                return span;
            }));

            return word;
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

    const couponName = done.querySelector('[data-done-name]');
    const couponNumber = done.querySelector('[data-done-number]');
    const couponStamp = done.querySelector('[data-coupon-stamp]');
    let sequenceTimers = [];

    const later = (delay, step) => {
        sequenceTimers.push(setTimeout(step, prefersReducedMotion ? 0 : delay));
    };

    const stopSequence = () => {
        sequenceTimers.forEach(clearTimeout);
        sequenceTimers = [];
    };

    const bringIntoView = (element) => {
        requestAnimationFrame(() => {
            const box = element.getBoundingClientRect();

            if (box.top < 0 || box.bottom > window.innerHeight) {
                element.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'center' });
            }
        });
    };

    /**
     * After the reply: the control coupon prints out, the guest's name flips
     * in, the coupon is punched, then stamped. Everything happens where the
     * guest is already looking, which on a phone is the form, not the stub.
     */
    const showTicket = (ticket) => {
        stopSequence();
        error.hidden = true;
        done.querySelector('[data-done-drink]').textContent = ticket.drink_phrase;
        fields.hidden = true;
        done.hidden = false;
        bringIntoView(done);

        if (!prefersReducedMotion) {
            restartAnimation(done, 'is-printing');
            couponStamp.classList.remove('is-stamping');
            couponStamp.classList.add('is-waiting');
        }

        couponName.style.setProperty('--flip-start', '250ms');
        flipText(couponName, ticket.name);
        rollNumber(couponNumber, ticket.number);

        rollNumber(stubNumber, ticket.number);
        flipText(stubGuest, ticket.name);
        stubGuest.classList.remove('is-empty');
        stubDrink.textContent = `${ticket.drink_code} · ${ticket.drink}`;

        later(700, () => {
            punchTicket(couponPunch, ticket.number, !prefersReducedMotion);
            if (!prefersReducedMotion) {
                shake(done);
            }
        });

        later(1250, () => {
            couponStamp.classList.remove('is-waiting');
            if (!prefersReducedMotion) {
                restartAnimation(couponStamp, 'is-stamping');
            }

            stamp.querySelector('[data-stamp-text]').textContent = 'Подтверждено';
            stamp.classList.add('is-confirmed');
            restartAnimation(stamp, 'is-restamped');
        });

        later(1450, () => {
            if (!prefersReducedMotion) {
                shake(done);
            }
        });

        later(1800, () => restartAnimation(chatLink, 'is-ready'));
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
        stopSequence();
        done.hidden = true;
        fields.hidden = false;
        firstNameInput.focus();
    });
}
