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

    const sponsorConsent = document.querySelector('[data-sponsor-consent]');
    const sponsorControls = document.querySelector('[data-sponsor-controls]');
    const sponsorList = document.querySelector('[data-sponsor-list]');
    const sponsorEmptyMessage = 'Список пока не опубликован. Попробуйте позже или обратитесь в группу.';

    function ensureSponsorEmptyMessage() {
        if (!sponsorList) {
            return null;
        }

        let message = sponsorList.querySelector('[data-sponsor-empty]');
        if (!message) {
            message = document.createElement('p');
            message.className = 'pauza-muted-line';
            message.setAttribute('data-sponsor-empty', '');
            message.textContent = sponsorEmptyMessage;
            message.hidden = true;
            sponsorList.appendChild(message);
        }

        return message;
    }

    if (sponsorConsent) {
        sponsorConsent.addEventListener('click', function () {
            if (sponsorControls) {
                sponsorControls.hidden = false;
            }
            if (sponsorList) {
                sponsorList.hidden = true;
            }
            sponsorConsent.setAttribute('aria-expanded', 'true');
            sponsorConsent.disabled = true;
            sponsorConsent.classList.add('is-confirmed');
            sponsorConsent.textContent = 'Правило принято';
            if (sponsorControls) {
                const firstFilter = sponsorControls.querySelector('[data-sponsor-filter]');
                if (firstFilter) {
                    firstFilter.focus();
                }
            }
        });
    }

    const filterButtons = document.querySelectorAll('[data-sponsor-filter]');
    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const filter = button.getAttribute('data-sponsor-filter');
            const sponsorCards = sponsorList ? Array.from(sponsorList.querySelectorAll('[data-sponsor-gender]')) : [];

            if (sponsorList) {
                sponsorList.hidden = false;
                sponsorList.classList.remove('is-collapsed');
            }

            filterButtons.forEach(function (item) {
                item.classList.toggle('is-active', item === button);
            });

            sponsorCards.forEach(function (card) {
                card.classList.add('is-hidden');
            });

            const visibleCards = shuffle(sponsorCards.filter(function (card) {
                return card.getAttribute('data-sponsor-gender') === filter;
            }));

            visibleCards.forEach(function (card) {
                card.classList.remove('is-hidden');
                sponsorList.appendChild(card);
            });

            const emptyMessage = ensureSponsorEmptyMessage();
            if (emptyMessage) {
                sponsorList.appendChild(emptyMessage);
                emptyMessage.hidden = visibleCards.length > 0;
            }
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

    document.querySelectorAll('.pauza-step-folders').forEach(function (container) {
        container.querySelectorAll('.pauza-step-folder').forEach(function (folder) {
            folder.addEventListener('toggle', function () {
                if (!folder.open) {
                    return;
                }

                container.querySelectorAll('.pauza-step-folder').forEach(function (other) {
                    if (other !== folder) {
                        other.open = false;
                    }
                });
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
