(function () {
    const travelersBox = document.querySelector('#travelers');
    if (!travelersBox) return;

    const formatMoney = (value) => new Intl.NumberFormat('ca-ES', { style: 'currency', currency: 'EUR' }).format(value);

    function getPrices() {
        return {
            adult: parseFloat(travelersBox.dataset.adultPrice || '0'),
            child: parseFloat(travelersBox.dataset.childPrice || '0'),
            single: parseFloat(travelersBox.dataset.singlePrice || '0'),
            superior: parseFloat(travelersBox.dataset.superiorPrice || '0')
        };
    }

    function recalculate() {
        const prices = getPrices();
        let total = 0;

        travelersBox.querySelectorAll('[data-traveler]').forEach((card, index) => {
            const number = card.querySelector('[data-traveler-number]');
            if (number) number.textContent = String(index + 1);

            card.querySelectorAll('input, select, textarea').forEach((field) => {
                field.name = field.name.replace(/viatgers\[\d+\]/, `viatgers[${index}]`);
            });

            const type = card.querySelector('select[name*="[adult]"]');
            const single = card.querySelector('input[name*="[habitacio_individual]"]');
            const superior = card.querySelector('input[name*="[categoria_superior]"]');

            let price = type && type.value === '1' ? prices.adult : prices.child;
            if (single && single.checked) price += prices.single;
            if (superior && superior.checked) price += prices.superior;

            total += price;
            const priceNode = card.querySelector('[data-traveler-price]');
            if (priceNode) priceNode.textContent = formatMoney(price);
        });

        const totalNode = document.querySelector('[data-booking-total]');
        if (totalNode) totalNode.textContent = formatMoney(total);
    }

    function cloneTraveler() {
        const first = travelersBox.querySelector('[data-traveler]');
        if (!first) return;
        const clone = first.cloneNode(true);
        clone.querySelectorAll('input, textarea').forEach((field) => {
            if (field.type === 'checkbox') {
                field.checked = false;
            } else {
                field.value = '';
            }
        });
        clone.querySelectorAll('select').forEach((field) => field.value = '1');
        travelersBox.appendChild(clone);
        recalculate();
    }

    document.addEventListener('click', function (event) {
        if (event.target.matches('[data-add-traveler]')) {
            cloneTraveler();
        }
        if (event.target.matches('[data-remove-traveler]')) {
            const cards = travelersBox.querySelectorAll('[data-traveler]');
            if (cards.length <= 1) return;
            event.target.closest('[data-traveler]').remove();
            recalculate();
        }
    });

    document.addEventListener('change', function (event) {
        if (event.target.matches('[data-price-trigger]')) recalculate();
    });

    recalculate();
})();
