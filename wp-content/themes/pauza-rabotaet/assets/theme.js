(function () {
    const menuButton = document.querySelector('.pauza-menu-toggle');
    const menu = document.querySelector('.pauza-nav');

    if (menuButton && menu) {
        function closeMenu() {
            menu.classList.remove('is-open');
            menuButton.setAttribute('aria-expanded', 'false');
        }

        menuButton.addEventListener('click', function () {
            const isOpen = menu.classList.toggle('is-open');
            menuButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        menu.addEventListener('click', function (event) {
            const link = event.target.closest('a');
            if (!link) {
                return;
            }

            closeMenu();
        });

        document.addEventListener('click', function (event) {
            if (!menu.classList.contains('is-open')) {
                return;
            }

            if (menu.contains(event.target) || menuButton.contains(event.target)) {
                return;
            }

            closeMenu();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeMenu();
            }
        });
    }

    const meetingsFloat = document.querySelector('[data-meetings-float]');
    const meetingsToggle = meetingsFloat ? meetingsFloat.querySelector('[data-meetings-toggle]') : null;
    const meetingsZoomLink = meetingsFloat ? meetingsFloat.querySelector('[data-meetings-zoom]') : null;

    if (meetingsFloat && meetingsToggle) {
        function setMeetingsOpen(isOpen) {
            meetingsFloat.classList.toggle('is-open', isOpen);
            meetingsToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (!isOpen && meetingsFloat.contains(document.activeElement) && typeof document.activeElement.blur === 'function') {
                document.activeElement.blur();
            }
        }

        meetingsToggle.addEventListener('click', function () {
            setMeetingsOpen(!meetingsFloat.classList.contains('is-open'));
        });

        document.addEventListener('click', function (event) {
            if (!meetingsFloat.classList.contains('is-open') || meetingsFloat.contains(event.target)) {
                return;
            }

            setMeetingsOpen(false);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            setMeetingsOpen(false);
        });

        if (meetingsZoomLink) {
            meetingsZoomLink.addEventListener('click', function (event) {
                const href = meetingsZoomLink.href;
                if (!href) {
                    return;
                }

                event.preventDefault();

                const opened = window.open(href, '_blank');
                if (opened) {
                    try {
                        opened.opener = null;
                    } catch (error) {
                        // Ignore browsers that disallow touching opener across contexts.
                    }
                } else {
                    window.location.href = href;
                }

                setMeetingsOpen(false);
            });
        }
    }

    const helpModal = document.querySelector('[data-help-modal]');
    const helpDialog = helpModal ? helpModal.querySelector('.pauza-help-modal__dialog') : null;
    const helpOpenButtons = Array.from(document.querySelectorAll('[data-help-open]'));
    const helpCloseButtons = helpModal ? Array.from(helpModal.querySelectorAll('[data-help-close]')) : [];

    if (helpModal && helpDialog && helpOpenButtons.length) {
        function setHelpOpen(isOpen) {
            helpModal.hidden = !isOpen;
            helpModal.classList.toggle('is-open', isOpen);
            helpOpenButtons.forEach(function (button) {
                button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            if (isOpen) {
                if (menu && menuButton) {
                    menu.classList.remove('is-open');
                    menuButton.setAttribute('aria-expanded', 'false');
                }
                window.setTimeout(function () {
                    helpDialog.focus();
                }, 0);
            }
        }

        helpOpenButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                setHelpOpen(true);
            });
        });

        helpCloseButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                setHelpOpen(false);
            });
        });

        helpModal.addEventListener('click', function (event) {
            if (event.target === helpModal) {
                setHelpOpen(false);
            }
        });

        helpDialog.addEventListener('click', function (event) {
            const link = event.target.closest('a');
            if (!link) {
                return;
            }

            setHelpOpen(false);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !helpModal.hidden) {
                setHelpOpen(false);
            }
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
    const sponsorHint = document.querySelector('[data-sponsor-hint]');
    const sponsorList = document.querySelector('[data-sponsor-list]');
    const sponsorEmptyMessage = 'Список пока не опубликован. Попробуйте позже или обратитесь в группу.';
    let copyToastTimer = null;

    function showCopyToast(message) {
        let toast = document.querySelector('[data-copy-toast]');
        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'pauza-copy-toast';
            toast.setAttribute('data-copy-toast', '');
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(copyToastTimer);
        copyToastTimer = window.setTimeout(function () {
            toast.classList.remove('is-visible');
        }, 1800);
    }

    function fallbackCopyText(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', '');
        textarea.style.position = 'fixed';
        textarea.style.left = '-9999px';
        textarea.style.top = '0';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();

        let copied = false;
        try {
            copied = document.execCommand('copy');
        } catch (error) {
            copied = false;
        }

        document.body.removeChild(textarea);
        return copied ? Promise.resolve() : Promise.reject(new Error('copy failed'));
    }

    function copyText(text) {
        if (!text) {
            return Promise.reject(new Error('empty text'));
        }

        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text).catch(function () {
                return fallbackCopyText(text);
            });
        }

        return fallbackCopyText(text);
    }

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

    let sponsorCardsLoaded = false;
    let sponsorCardsLoading = null;

    function createSponsorCard(sponsor) {
        const card = document.createElement('article');
        card.className = 'pauza-sponsor-card pauza-sponsor-card--compact is-hidden';
        card.hidden = true;
        card.setAttribute('data-sponsor-gender', sponsor.gender || 'female');
        card.setAttribute('data-nosnippet', '');

        const sponsorPhone = sponsor.phone || '';

        const title = document.createElement('h3');
        title.textContent = sponsor.name || '';
        card.appendChild(title);

        if (sponsorPhone) {
            const phoneButton = document.createElement('button');
            phoneButton.type = 'button';
            phoneButton.className = 'pauza-sponsor-copy pauza-sponsor-copy--phone';
            phoneButton.title = 'Скопировать номер';
            phoneButton.setAttribute('aria-label', 'Скопировать номер');
            phoneButton.setAttribute('data-sponsor-copy', 'phone');
            phoneButton.setAttribute('data-copy-text', sponsorPhone);
            phoneButton.textContent = sponsorPhone;
            card.appendChild(phoneButton);
        }

        return card;
    }

    function existingSponsorCards() {
        return sponsorList ? Array.from(sponsorList.querySelectorAll('[data-sponsor-gender]')) : [];
    }

    function loadSponsorCards() {
        if (!sponsorList || sponsorCardsLoaded) {
            return Promise.resolve(existingSponsorCards());
        }

        if (sponsorCardsLoading) {
            return sponsorCardsLoading;
        }

        if (!window.pauzaSponsorApi || !window.pauzaSponsorApi.url) {
            sponsorCardsLoaded = true;
            return Promise.resolve(existingSponsorCards());
        }

        const body = new URLSearchParams();
        body.set('action', window.pauzaSponsorApi.action || 'pauza_sponsors');
        body.set('nonce', window.pauzaSponsorApi.nonce || '');
        sponsorList.setAttribute('aria-busy', 'true');

        sponsorCardsLoading = fetch(window.pauzaSponsorApi.url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
            },
            body: body.toString(),
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (payload) {
                sponsorCardsLoaded = true;
                sponsorList.removeAttribute('aria-busy');

                if (!payload || !payload.success || !payload.data || !Array.isArray(payload.data.sponsors)) {
                    return existingSponsorCards();
                }

                payload.data.sponsors.forEach(function (sponsor) {
                    sponsorList.appendChild(createSponsorCard(sponsor));
                });

                return existingSponsorCards();
            })
            .catch(function () {
                sponsorCardsLoaded = true;
                sponsorList.removeAttribute('aria-busy');

                return existingSponsorCards();
            });

        return sponsorCardsLoading;
    }

    if (sponsorConsent) {
        sponsorConsent.addEventListener('click', function () {
            if (sponsorControls) {
                sponsorControls.hidden = false;
            }
            if (sponsorHint) {
                sponsorHint.hidden = false;
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

    if (sponsorList) {
        sponsorList.addEventListener('click', function (event) {
            const button = event.target.closest('[data-sponsor-copy]');
            if (!button || !sponsorList.contains(button)) {
                return;
            }

            event.preventDefault();
            copyText(button.getAttribute('data-copy-text') || '')
                .then(function () {
                    showCopyToast('Номер скопирован');
                })
                .catch(function () {
                    showCopyToast('Не удалось скопировать');
                });
        });
    }

    const filterButtons = document.querySelectorAll('[data-sponsor-filter]');
    function sponsorFilterFromButton(button) {
        const label = (button.textContent || '').trim().toLowerCase();

        if (label.indexOf('жен') !== -1) {
            return 'female';
        }

        if (label.indexOf('муж') !== -1) {
            return 'male';
        }

        return button.getAttribute('data-sponsor-filter');
    }

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const filter = sponsorFilterFromButton(button);

            if (sponsorList) {
                sponsorList.hidden = false;
                sponsorList.classList.remove('is-collapsed');
            }

            filterButtons.forEach(function (item) {
                item.classList.toggle('is-active', item === button);
            });

            loadSponsorCards().then(function (sponsorCards) {
                sponsorCards.forEach(function (card) {
                    card.hidden = true;
                    card.classList.add('is-hidden');
                });

                const visibleCards = shuffle(sponsorCards.filter(function (card) {
                    return card.getAttribute('data-sponsor-gender') === filter;
                }));

                visibleCards.forEach(function (card) {
                    card.hidden = false;
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
