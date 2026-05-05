(function () {
    const menuButton = document.querySelector('.pauza-menu-toggle');
    const menu = document.querySelector('.pauza-nav');

    if (menuButton && menu) {
        menuButton.addEventListener('click', function () {
            const isOpen = menu.classList.toggle('is-open');
            menuButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    const filterButtons = document.querySelectorAll('[data-sponsor-filter]');
    const sponsorCards = document.querySelectorAll('[data-sponsor-gender]');

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const filter = button.getAttribute('data-sponsor-filter');

            filterButtons.forEach(function (item) {
                item.classList.toggle('is-active', item === button);
            });

            sponsorCards.forEach(function (card) {
                const gender = card.getAttribute('data-sponsor-gender');
                card.classList.toggle('is-hidden', filter !== 'all' && gender !== filter);
            });
        });
    });

    document.querySelectorAll('[data-pauza-tabs]').forEach(function (tabs) {
        const buttons = Array.from(tabs.querySelectorAll('[data-tab-target]'));
        const panels = Array.from(tabs.querySelectorAll('[data-tab-panel]'));

        function activateTab(target) {
            buttons.forEach(function (button) {
                const isActive = button.getAttribute('data-tab-target') === target;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-selected', isActive ? 'true' : 'false');
                button.setAttribute('tabindex', isActive ? '0' : '-1');
            });

            panels.forEach(function (panel) {
                const isActive = panel.getAttribute('data-tab-panel') === target;
                panel.classList.toggle('is-active', isActive);
                panel.hidden = !isActive;
            });
        }

        buttons.forEach(function (button, index) {
            button.addEventListener('click', function () {
                activateTab(button.getAttribute('data-tab-target'));
            });

            button.addEventListener('keydown', function (event) {
                if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') {
                    return;
                }

                event.preventDefault();
                const direction = event.key === 'ArrowRight' ? 1 : -1;
                const nextIndex = (index + direction + buttons.length) % buttons.length;
                buttons[nextIndex].focus();
                activateTab(buttons[nextIndex].getAttribute('data-tab-target'));
            });
        });
    });
})();
