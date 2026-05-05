(function () {
    const menuButton = document.querySelector('.pauza-menu-toggle');
    const menu = document.querySelector('.pauza-nav');

    if (menuButton && menu) {
        menuButton.addEventListener('click', function () {
            const isOpen = menu.classList.toggle('is-open');
            menuButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    function shuffle(items) {
        const copy = items.slice();
        for (let index = copy.length - 1; index > 0; index -= 1) {
            const randomIndex = Math.floor(Math.random() * (index + 1));
            const current = copy[index];
            copy[index] = copy[randomIndex];
            copy[randomIndex] = current;
        }
        return copy;
    }

    function openStepFromHash(hash, shouldScroll) {
        if (!/^#step-\d+$/.test(hash || '')) {
            return false;
        }

        const step = document.querySelector(hash);
        if (!step || !step.classList.contains('pauza-step-folder')) {
            return false;
        }

        const container = step.closest('.pauza-step-folders');
        if (container) {
            container.querySelectorAll('.pauza-step-folder').forEach(function (folder) {
                folder.open = folder === step;
            });
        } else {
            step.open = true;
        }

        if (shouldScroll) {
            const scrollToStep = function () {
                const target = step.querySelector('.pauza-step-folder__summary') || step;
                const offset = 88;
                const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({ top: Math.max(0, top), behavior: 'auto' });
            };

            window.setTimeout(scrollToStep, 30);
            window.setTimeout(scrollToStep, 250);
            window.setTimeout(scrollToStep, 800);
        }

        return true;
    }

    const filterButtons = document.querySelectorAll('[data-sponsor-filter]');
    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const filter = button.getAttribute('data-sponsor-filter');
            const sponsorList = document.querySelector('[data-sponsor-list]');
            const sponsorCards = sponsorList ? Array.from(sponsorList.querySelectorAll('[data-sponsor-gender]')) : [];

            if (sponsorList) {
                sponsorList.classList.remove('is-collapsed');
                sponsorList.querySelectorAll('p:not([class])').forEach(function (placeholder) {
                    placeholder.remove();
                });
            }

            filterButtons.forEach(function (item) {
                item.classList.toggle('is-active', item === button);
            });

            sponsorCards.forEach(function (card) {
                card.classList.add('is-hidden');
            });

            shuffle(sponsorCards.filter(function (card) {
                return card.getAttribute('data-sponsor-gender') === filter;
            })).forEach(function (card) {
                card.classList.remove('is-hidden');
                sponsorList.appendChild(card);
            });
        });
    });

    document.addEventListener('click', function (event) {
        const link = event.target.closest('a[href^="#step-"]');
        if (!link) {
            return;
        }

        const hash = link.getAttribute('href');
        if (openStepFromHash(hash, true)) {
            event.preventDefault();
            history.pushState(null, '', hash);
            if (menu && menuButton) {
                menu.classList.remove('is-open');
                menuButton.setAttribute('aria-expanded', 'false');
            }
        }
    });

    document.addEventListener('click', function (event) {
        const summary = event.target.closest('.pauza-step-folder__summary');
        if (!summary) {
            return;
        }

        window.setTimeout(function () {
            const offset = 88;
            const top = summary.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top: Math.max(0, top), behavior: 'auto' });
        }, 80);
    });

    document.addEventListener('pauza:steps-ready', function () {
        openStepFromHash(window.location.hash, true);
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
