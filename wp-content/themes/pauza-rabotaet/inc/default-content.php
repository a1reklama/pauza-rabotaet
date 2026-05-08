<?php
/**
 * Starter content imported from the owner's DOCX and reshaped for MVP UX.
 *
 * @package PauzaRabotaet
 */

if (!defined('ABSPATH')) {
    exit;
}

function pauza_seed_default_content(): void
{
    if (get_option('pauza_seeded_v1')) {
        return;
    }

    pauza_seed_options();

    $home_id = pauza_seed_page(
        'glavnaya',
        '12 шагов для ВСЕХ',
        'Понятный вход в программу: выбрать спонсора, открыть первый шаг, пользоваться калькулятором и переходить в нужные группы.',
        ''
    );

    $calculator_id = pauza_seed_page(
        'calculator',
        'Калькуляторы',
        'Калькуляторы открываются во внешних ботах Telegram и MAX. Сайт дает ссылки и объясняет, куда отправлять результат.',
        ''
    );

    $bot_id = pauza_seed_page(
        'bot-4-shaga',
        'Бот 4 шага',
        'Работа по четвертому шагу выполняется во внешнем боте. Сайт объясняет, когда туда переходить, и дает кнопку входа.',
        ''
    );

    if ($home_id) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_id);
    }

    pauza_seed_steps();
    pauza_seed_materials();
    pauza_seed_today();
    pauza_retire_seeded_news();
    pauza_seed_sponsors();
    pauza_seed_menu($home_id, $calculator_id, $bot_id);

    update_option('pauza_seeded_v1', current_time('mysql'));
}
add_action('after_switch_theme', 'pauza_seed_default_content', 20);
add_action('after_switch_theme', 'pauza_seed_step_full_texts_for_existing_posts', 30);
add_action('after_switch_theme', 'pauza_seed_step_structured_blocks_for_existing_posts', 31);
add_action('admin_init', 'pauza_seed_step_full_texts_for_existing_posts');
add_action('admin_init', 'pauza_seed_step_structured_blocks_for_existing_posts');
add_action('admin_init', 'pauza_ensure_today_seeded');
add_action('admin_init', 'pauza_ensure_default_menu_items');

function pauza_ensure_today_seeded(): void
{
    if (get_option('pauza_today_seeded_v2')) {
        return;
    }

    pauza_seed_today();
    pauza_retire_seeded_news();
    update_option('pauza_today_seeded_v2', current_time('mysql'));
}

function pauza_ensure_default_menu_items(): void
{
    if (get_option('pauza_menu_seeded_v4')) {
        return;
    }

    $home = get_page_by_path('glavnaya', OBJECT, 'page');
    $calculator = get_page_by_path('calculator', OBJECT, 'page');
    $bot = get_page_by_path('bot-4-shaga', OBJECT, 'page');

    pauza_seed_menu(
        $home instanceof WP_Post ? (int) $home->ID : 0,
        $calculator instanceof WP_Post ? (int) $calculator->ID : 0,
        $bot instanceof WP_Post ? (int) $bot->ID : 0
    );
}

function pauza_seed_step_full_texts_for_existing_posts(): void
{
    if (get_option('pauza_full_text_seeded_v2')) {
        return;
    }

    $full_texts = pauza_load_step_full_texts();
    if (!$full_texts) {
        return;
    }

    $query = new WP_Query([
        'post_type'      => 'pauza_step',
        'post_status'    => ['publish', 'draft', 'pending', 'private'],
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    foreach ($query->posts as $post_id) {
        $number = pauza_meta((int) $post_id, '_pauza_step_number');
        if ($number && isset($full_texts[$number])) {
            update_post_meta((int) $post_id, '_pauza_step_full_text', $full_texts[$number]);
        }
    }

    update_option('pauza_full_text_seeded_v2', current_time('mysql'));
}

function pauza_seed_step_structured_blocks_for_existing_posts(): void
{
    if (get_option('pauza_step_blocks_seeded_v3')) {
        return;
    }

    $blocks = pauza_step_structured_blocks();
    if (!$blocks) {
        return;
    }

    $query = new WP_Query([
        'post_type'      => 'pauza_step',
        'post_status'    => ['publish', 'draft', 'pending', 'private'],
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    foreach ($query->posts as $post_id) {
        $number = pauza_meta((int) $post_id, '_pauza_step_number');
        if (!$number || !isset($blocks[$number])) {
            continue;
        }

        update_post_meta((int) $post_id, '_pauza_step_materials', implode("\n", $blocks[$number]['materials']));
        update_post_meta((int) $post_id, '_pauza_step_exercises', $blocks[$number]['exercises']);
    }

    update_option('pauza_step_blocks_seeded_v3', current_time('mysql'));
}

function pauza_seed_options(): void
{
    $existing = get_option('pauza_options', []);
    $existing = is_array($existing) ? $existing : [];

    $defaults = [
        'telegram_channel_url'       => 'https://t.me/neporukovodstvu',
        'rutube_channel_url'         => 'https://rutube.ru/channel/44350949/',
        'yandex_disk_url'            => 'https://disk.yandex.ru/d/N2hLvMMUeXxsjg',
        'four_step_bot_url'          => 'https://t.me/FourStepForAllBot',
        'four_step_max_bot_url'      => 'https://max.ru/id860230186705_bot',
        'calculator_instruction_url' => 'https://rutube.ru/video/7e631d8d1d40f7cbe4f68d1a321a3f10/',
        'calculator_telegram_url'    => '',
        'calculator_max_url'         => '',
        'calculator_intro'           => 'Калькулятор открыт как отдельный веб-сервис. Сайт ведет на него и не хранит ответы пользователя.',
        'privacy_notice'             => 'Публично показываются только подтвержденные контакты. Большая просьба не звонить: сначала напишите сообщение, представьтесь и коротко расскажите о себе.',
        'footer_note'                => 'Сайт помогает ориентироваться в программе и ведет во внешние группы, боты и видеоматериалы. Он не заменяет работу со спонсором.',
    ];

    update_option('pauza_options', array_merge($defaults, $existing));
}

function pauza_seed_page(string $slug, string $title, string $content, string $template = ''): int
{
    $existing = get_page_by_path($slug, OBJECT, 'page');
    if ($existing instanceof WP_Post) {
        return (int) $existing->ID;
    }

    $page_id = wp_insert_post([
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_content' => $content,
    ], true);

    if (is_wp_error($page_id)) {
        return 0;
    }

    if ($template) {
        update_post_meta((int) $page_id, '_wp_page_template', $template);
    }

    return (int) $page_id;
}

function pauza_seed_post(string $post_type, string $slug, string $title, string $content, array $meta = [], string $status = 'publish', int $menu_order = 0): int
{
    $existing = get_page_by_path($slug, OBJECT, $post_type);
    if ($existing instanceof WP_Post) {
        return (int) $existing->ID;
    }

    $post_id = wp_insert_post([
        'post_type'    => $post_type,
        'post_status'  => $status,
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_content' => $content,
        'post_excerpt' => wp_trim_words(wp_strip_all_tags($content), 26),
        'menu_order'   => $menu_order,
    ], true);

    if (is_wp_error($post_id)) {
        return 0;
    }

    foreach ($meta as $key => $value) {
        update_post_meta((int) $post_id, $key, $value);
    }

    return (int) $post_id;
}

function pauza_seed_steps(): void
{
    $full_texts = pauza_load_step_full_texts();
    $step_blocks = pauza_step_structured_blocks();
    $steps = [
        [
            'number' => 1,
            'title' => '1 шаг для ВСЕХ',
            'status' => 'Старт',
            'goal' => 'Признать бессилие перед мыслями и желаниями и начать ежедневную работу с паузой.',
            'content' => 'Первый шаг нужен, чтобы человек не потерялся в программе: выбрать спонсора, открыть первые видео, начать калькулятор и выполнить первые письменные задания.',
            'requirements' => [
                'Выбрать спонсора своего пола.',
                'Не начинать работу в одиночку, если есть возможность написать спонсору или в группу.',
            ],
            'tasks' => [
                'Написать спонсору или в группу первого шага.',
                'Посмотреть вводное видео и инструкцию к калькулятору.',
                'Каждый день работать в калькуляторе и отправлять результат.',
                'Выписать текст первого шага и носить его с собой.',
                'Составить топ-5 зависимостей и разобрать каждую по пяти вопросам.',
                'После завершения работы встретиться со спонсором очно или онлайн.',
            ],
            'telegram' => 'https://t.me/+oqVGHQ3VtMQ3YTEy',
            'max' => 'https://max.ru/c/-70999405442331/AZ2mljynXQs',
            'next_label' => 'Перейти ко 2 шагу',
            'next_url' => home_url('/12-shagov/vtoroy-shag/'),
        ],
        [
            'number' => 2,
            'title' => '2 шаг для ВСЕХ',
            'status' => 'Дни 9-18',
            'goal' => 'Попытаться поверить, что внутренняя сила поможет брать паузу и жить без вреда себе и другим.',
            'content' => 'Второй шаг переводит работу от признания проблемы к опыту надежды: человек смотрит видео, отвечает на вопросы о свободе от зависимостей и обсуждает работу со спонсором.',
            'requirements' => [
                'Актуальный спонсор выбран.',
                'Первый шаг прочитан или согласован со спонсором.',
            ],
            'tasks' => [
                'Смотреть видео шага по порядку.',
                'Записать текст второго шага.',
                'Ответить по каждой из пяти зависимостей: какими станут отношения в свободе.',
                'Прочитать дополнительные материалы только в составе шага, не как отдельную длинную страницу.',
                'Подвести итоги и зачитать работу спонсору.',
            ],
            'telegram' => 'https://t.me/+twnI1QAhz_RmOWYy',
            'max' => 'https://max.ru/join/mU9LyOoSp4JnQ6czN2H7ex7EsF3PBDLynhYBVujyUGg',
            'next_label' => 'Перейти к 3 шагу',
            'next_url' => home_url('/12-shagov/tretiy-shag/'),
        ],
        [
            'number' => 3,
            'title' => '3 шаг для ВСЕХ',
            'status' => 'Перед 4 шагом',
            'goal' => 'Научиться чаще брать паузу и передавать решение Богу своего понимания.',
            'content' => 'Третий шаг закрепляет базовые понятия, молитвы и глоссарий, чтобы человек был готов к глубокой письменной работе четвертого шага.',
            'requirements' => [
                'Выбран спонсор.',
                'Второй шаг завершен и обсужден.',
            ],
            'tasks' => [
                'Смотреть видео третьего шага по порядку.',
                'Записать текст третьего шага и короткую фразу для паузы.',
                'Ответить на вопросы об ответственности человека и Бога в первых трех шагах.',
                'Пройти упражнение на знание глоссария.',
                'Встретиться со спонсором и зачитать работу.',
                'После завершения перейти в бот 4 шага.',
            ],
            'telegram' => 'https://t.me/+T3CBBPM83MgyYTli',
            'max' => 'https://max.ru/join/wDgk_cNitW-GQJ5xL5ZgMli62mjSa7ydeKzY5plqFYs',
            'next_label' => 'Открыть бот 4 шага',
            'next_url' => 'https://t.me/FourStepForAllBot',
        ],
        [
            'number' => 4,
            'title' => '4 шаг для ВСЕХ',
            'status' => 'Внешний бот',
            'goal' => 'Выполнить глубокую письменную инвентаризацию в отдельном инструменте.',
            'content' => 'В MVP сайт не переносит работу четвертого шага внутрь WordPress. Он объясняет точку перехода и ведет в Telegram-бот, где находится рабочий функционал.',
            'requirements' => [
                'Третий шаг завершен.',
                'Есть спонсор для сопровождения.',
            ],
            'tasks' => [
                'Открыть Telegram-бот 4 шага.',
                'Работать по инструкциям бота.',
                'Не публиковать чувствительные ответы на сайте.',
                'После завершения согласовать чтение 5 шага со спонсором.',
            ],
            'telegram' => 'https://t.me/FourStepForAllBot',
            'max' => '',
            'next_label' => 'Открыть страницу бота',
            'next_url' => home_url('/bot-4-shaga/'),
        ],
        [
            'number' => 5,
            'title' => '5 шаг для ВСЕХ',
            'status' => 'Со спонсором',
            'goal' => 'Прочитать работу по четвертому шагу спонсору и подготовиться к следующим шагам.',
            'content' => 'В исходном документе пятый шаг не раскрыт как отдельная публичная инструкция. На сайте его нужно держать коротким мостом между ботом 4 шага и группой 6 шага.',
            'requirements' => [
                'Четвертый шаг выполнен в боте или другом утвержденном формате.',
                'Встреча со спонсором согласована заранее.',
            ],
            'tasks' => [
                'Договориться со спонсором о чтении работы.',
                'Не выкладывать материалы пятого шага на сайт.',
                'После чтения перейти в группу 6 шага.',
            ],
            'telegram' => '',
            'max' => '',
            'next_label' => 'Перейти к 6 шагу',
            'next_url' => home_url('/12-shagov/shestoy-shag/'),
        ],
        [
            'number' => 6,
            'title' => '6 шаг для ВСЕХ',
            'status' => 'После 5 шага',
            'goal' => 'Стать готовым к работе с дефектами характера после чтения пятого шага.',
            'content' => 'Шестой шаг начинается после чтения пятого шага спонсору. Сайт показывает условия входа, группу и короткий алгоритм, а ежедневная работа идет во внешней группе.',
            'requirements' => [
                'Пятый шаг прочитан спонсору.',
                'Есть актуальный спонсор из списка сайта.',
            ],
            'tasks' => [
                'Войти в группу 6 шага.',
                'Смотреть видео шага по порядку.',
                'Работать с готовностью к изменениям.',
                'После завершения перейти в группу 7 шага.',
            ],
            'telegram' => 'https://t.me/Step6forall',
            'max' => 'https://max.ru/join/l5VBd6caSGy1Y7o7CnUvDkc9Tt0oswP3qMOABt_pST8',
            'next_label' => 'Перейти к 7 шагу',
            'next_url' => home_url('/12-shagov/sedmoy-shag/'),
        ],
        [
            'number' => 7,
            'title' => '7 шаг для ВСЕХ',
            'status' => 'Практика по дефектам',
            'goal' => 'Просить помощи в воздержании от недостатков и ежедневно подводить итоги.',
            'content' => 'В седьмом шаге работа идет по дефектам характера: утром просьба о помощи, днем наблюдение, вечером короткий отчет в группу или спонсору.',
            'requirements' => [
                'Пятый шаг прочитан.',
                'Шестой шаг пройден.',
            ],
            'tasks' => [
                'Смотреть видео 7 шага за соответствующий день.',
                'Работать с каждым дефектом минимум 3 дня или дольше.',
                'Утром просить помощи воздерживаться от слов и действий на дефекте.',
                'Вечером писать опыт по этому дефекту в группу или спонсору.',
                'После завершения перейти в 8 шаг.',
            ],
            'telegram' => 'https://t.me/+kDj7ZR1L1X9iYjYy',
            'max' => 'https://max.ru/join/k7yW0p_DISlAKzJfy8hkvLkEEaLac2ZtlvOcxsjEcfk',
            'next_label' => 'Перейти к 8 шагу',
            'next_url' => home_url('/12-shagov/vosmoy-shag/'),
        ],
        [
            'number' => 8,
            'title' => '8 шаг для ВСЕХ',
            'status' => 'Список ущерба',
            'goal' => 'Составить список людей и организаций, которым был причинен вред, и подготовить готовность к возмещению.',
            'content' => 'Восьмой шаг структурирует списки вреда и ущерба. Длинные упражнения на сайте лучше держать в раскрывающихся блоках, чтобы человек двигался по одному списку за раз.',
            'requirements' => [
                'Пятый шаг прочитан.',
                'Шаги 6 и 7 пройдены.',
            ],
            'tasks' => [
                'Смотреть видео 8 шага по порядку.',
                'Составить списки физического, психического, материального и духовного вреда.',
                'Считать материальный ущерб аккуратно, при необходимости используя курс ЦБ на дату.',
                'Выполнить упражнение ВДА по инструкции шага.',
                'После завершения перейти в группу 9 шага.',
            ],
            'telegram' => 'https://t.me/+25xg0R3EbdExMTUy',
            'max' => 'https://max.ru/join/k5TJeDCbEZXvz08D8DhX2TRB2KPAY7dXOnsQDnVu0vk',
            'next_label' => 'Перейти к 9 шагу',
            'next_url' => home_url('/12-shagov/devyatyy-shag/'),
        ],
        [
            'number' => 9,
            'title' => '9 шаг для ВСЕХ',
            'status' => 'Возмещение ущерба',
            'goal' => 'Подготовить письма, согласовать план выхода и возмещать ущерб без оправданий.',
            'content' => 'Девятый шаг не должен превращаться в публичную выкладку писем. Сайт объясняет алгоритм и отправляет человека в группу/к спонсору для согласования.',
            'requirements' => [
                'Пятый шаг прочитан.',
                'Шаги 6, 7 и 8 пройдены.',
            ],
            'tasks' => [
                'Написать письмо каждому человеку из списка 8 шага.',
                'Не выкладывать письма в группу и на сайт.',
                'Составить план выхода и согласовать его со спонсором.',
                'Встретиться, позвонить или выполнить другой согласованный формат.',
                'Составить отдельный график материальных возмещений.',
            ],
            'telegram' => 'https://t.me/+G6EnY1_JYl5mNTAy',
            'max' => 'https://max.ru/join/jB0CedcE8uD9ZqhVzQomu64n603N3A3Ml-HSxz0YE2c',
            'next_label' => 'Перейти к 10 шагу',
            'next_url' => home_url('/12-shagov/desyatyy-shag/'),
        ],
        [
            'number' => 10,
            'title' => '10 шаг для ВСЕХ',
            'status' => 'Пожизненно',
            'goal' => 'Каждый день замечать вред, признавать дефект и возмещать ущерб до конца дня.',
            'content' => 'Десятый шаг запускает пожизненную практику: вечером выбрать одну главную ситуацию дня и разобрать ее через опыт 4, 9 и 7 шагов.',
            'requirements' => [
                'Девятый шаг начат или согласован со спонсором.',
                'Готовность вести ежедневную практику.',
            ],
            'tasks' => [
                'Вечером выбрать одну главную ситуацию дня.',
                'Разобрать, был ли вред и в чем дефект.',
                'До конца дня возместить вред, если он был.',
                'Попросить заменить дефект на соответствующий духовный принцип.',
                'Через 30 дней перейти в 11 шаг.',
            ],
            'telegram' => 'https://t.me/+1JO2hraTAbpiZjVi',
            'max' => 'https://max.ru/join/rtORKqtDJ-rWxds_X_WXgTXRcIEPd4PsUtA2y0U1dKI',
            'next_label' => 'Перейти к 11 шагу',
            'next_url' => home_url('/12-shagov/odinnadtsatyy-shag/'),
        ],
        [
            'number' => 11,
            'title' => '11 шаг для ВСЕХ',
            'status' => 'Пожизненно',
            'goal' => 'Улучшать осознанный контакт с Богом своего понимания через молитву и медитацию.',
            'content' => 'Одиннадцатый шаг остается практикой на всю жизнь. Сайт показывает простой алгоритм, а опыт можно писать в группу по желанию.',
            'requirements' => [
                'Десятый шаг практикуется минимум 30 дней.',
                'Есть готовность к молитве и медитации в своем понимании.',
            ],
            'tasks' => [
                'Слушать спикерские на канале по одной в день.',
                'Каждый день просить узнать волю Бога и силы ее исполнить.',
                'Медитировать и пытаться услышать ответ.',
                'После прослушивания всех спикерских ответить на вопросы шага.',
                'Перейти в группу 12 шага.',
            ],
            'telegram' => 'https://t.me/+T3t84Kpn0dkwNDg6',
            'max' => 'https://max.ru/join/zl41dRDCpkUJi31P31yjfJ8RWDzU528ICgzLuYdACNg',
            'next_label' => 'Перейти к 12 шагу',
            'next_url' => home_url('/12-shagov/dvenadtsatyy-shag/'),
        ],
        [
            'number' => 12,
            'title' => '12 шаг для ВСЕХ',
            'status' => 'Пожизненно',
            'goal' => 'Нести весть зависимым и применять принципы во всех делах.',
            'content' => 'Двенадцатый шаг фокусируется только на словах и действиях, связанных с несением вести и работой с подспонсорными, учениками, наставляемыми и реабилитационными центрами.',
            'requirements' => [
                'Шаги 10 и 11 стали регулярной практикой.',
                'Есть понимание границ помощи другим.',
            ],
            'tasks' => [
                'Писать в группу только опыт, прямо связанный с несением вести.',
                'Отделять работу с другими от личных дневниковых тем.',
                'Продолжать практиковать шаги 10-12 пожизненно.',
            ],
            'telegram' => 'https://t.me/+rpXrqsB2pNkxZTcy',
            'max' => 'https://max.ru/join/DHIw7LGiFytV9HoXKMYD81a-lxROi24yAR0eywD7NI8',
            'next_label' => 'Вернуться к карте шагов',
            'next_url' => home_url('/12-shagov/'),
        ],
    ];

    foreach ($steps as $step) {
        $slug = pauza_step_slug((int) $step['number'], $step['title']);
        pauza_seed_post('pauza_step', $slug, $step['title'], $step['content'], [
            '_pauza_step_number'       => (string) $step['number'],
            '_pauza_step_status'       => $step['status'],
            '_pauza_step_goal'         => $step['goal'],
            '_pauza_step_requirements' => implode("\n", $step['requirements']),
            '_pauza_step_tasks'        => implode("\n", $step['tasks']),
            '_pauza_step_materials'    => isset($step_blocks[(string) $step['number']]) ? implode("\n", $step_blocks[(string) $step['number']]['materials']) : '',
            '_pauza_step_exercises'    => $step_blocks[(string) $step['number']]['exercises'] ?? '',
            '_pauza_step_full_text'    => $full_texts[(string) $step['number']] ?? '',
            '_pauza_step_telegram_url' => $step['telegram'],
            '_pauza_step_max_url'      => $step['max'],
            '_pauza_step_video_url'    => '',
            '_pauza_step_next_label'   => $step['next_label'],
            '_pauza_step_next_url'     => $step['next_url'],
        ], 'publish', (int) $step['number']);
    }
}

function pauza_step_structured_blocks(): array
{
    $full_texts = pauza_load_step_full_texts();
    $blocks = [];

    foreach (range(1, 12) as $number) {
        $key = (string) $number;
        $text = isset($full_texts[$key]) ? (string) $full_texts[$key] : '';
        $numbered = $text ? pauza_step_numbered_lines($text) : [];
        $exercises = array_values(array_filter($numbered, static function ($line) {
            return !preg_match('/https?:\/\//i', (string) $line);
        }));

        $blocks[$key] = [
            'materials' => $text ? pauza_step_material_lines($text) : [],
            'exercises' => implode("\n", $exercises),
        ];
    }

    return $blocks;
}

function pauza_load_step_full_texts(): array
{
    $path = PAUZA_THEME_DIR . '/inc/step-full-texts.json';
    if (!file_exists($path) || !is_readable($path)) {
        return [];
    }

    $json = file_get_contents($path);
    if (false === $json) {
        return [];
    }

    $data = json_decode($json, true);

    return is_array($data) ? $data : [];
}

function pauza_step_slug(int $number, string $title): string
{
    $slugs = [
        1  => 'pervyy-shag',
        2  => 'vtoroy-shag',
        3  => 'tretiy-shag',
        4  => 'chetvertyy-shag',
        5  => 'pyatyy-shag',
        6  => 'shestoy-shag',
        7  => 'sedmoy-shag',
        8  => 'vosmoy-shag',
        9  => 'devyatyy-shag',
        10 => 'desyatyy-shag',
        11 => 'odinnadtsatyy-shag',
        12 => 'dvenadtsatyy-shag',
    ];

    return $slugs[$number] ?? sanitize_title($title);
}

function pauza_seed_materials(): void
{
    pauza_retire_seeded_post('pauza_material', 'calculator-telegram-bot');
    pauza_retire_seeded_post('pauza_material', 'calculator-max-bot');

    $materials = [
        [
            'slug' => 'telegram-video',
            'title' => 'Telegram-канал проекта',
            'content' => 'Основной канал проекта с видео и объявлениями. Это канал проекта, а не бот.',
            'type' => 'project_channel',
            'url' => 'https://t.me/neporukovodstvu',
            'label' => 'Открыть Telegram',
        ],
        [
            'slug' => 'rutube-video',
            'title' => 'Видео на Rutube',
            'content' => 'Дублирующий видеоканал. Конкретные видео лучше показывать внутри нужного шага.',
            'type' => 'video',
            'url' => 'https://rutube.ru/channel/44350949/',
            'label' => 'Открыть Rutube',
        ],
        [
            'slug' => 'yandex-disk-video',
            'title' => 'Видео на Яндекс.Диске',
            'content' => 'Архив видео, который можно скачать.',
            'type' => 'download',
            'url' => 'https://disk.yandex.ru/d/N2hLvMMUeXxsjg',
            'label' => 'Открыть диск',
        ],
        [
            'slug' => 'bot-4-shaga-telegram',
            'title' => 'Telegram-бот 4 шага',
            'content' => 'Внешний бот для работы по четвертому шагу. Показывается как инструмент шага, а не как постоянный пункт меню.',
            'type' => 'bot',
            'url' => 'https://t.me/FourStepForAllBot',
            'label' => 'Открыть бота',
        ],
        [
            'slug' => 'vosmoy-shag-telegram-group',
            'title' => 'Telegram-группа 8 шага',
            'content' => 'Группа конкретного шага. Ее не смешиваем с общими каналами проекта и ботами.',
            'type' => 'step_group',
            'url' => 'https://t.me/+25xg0R3EbdExMTUy',
            'label' => 'Открыть группу',
        ],
        [
            'slug' => 'calculator-web-service',
            'title' => 'Калькулятор выздоровления',
            'content' => 'Единый внешний веб-сервис калькулятора. Сайт ведет на него и не хранит ответы пользователя.',
            'type' => 'calculator',
            'url' => pauza_calculator_url(),
            'label' => 'Открыть калькулятор',
        ],
    ];

    foreach ($materials as $material) {
        pauza_seed_post('pauza_material', $material['slug'], $material['title'], $material['content'], [
            '_pauza_material_type'         => $material['type'],
            '_pauza_material_url'          => $material['url'],
            '_pauza_material_button_label' => $material['label'],
        ], 'publish');
    }
}

function pauza_seed_today(): void
{
    pauza_retire_seeded_post('pauza_today', 'tolko-segodnya-start');

    $items = [
        [
            'slug' => 'tolko-segodnya-vopros-082',
            'title' => 'Вопрос 082',
            'content' => implode("\n\n", [
                'Как ты понимаешь это предложение: «Ты становишься самим собой, и вот когда ты полностью станешь тем, кто ты есть на самом деле – только тогда ты по-настоящему выполнишь волю Бога». Относится ли это к тебе?',
                'Нет ко мне это не относится. Волю Бога я и так могу исполнять, мне не надо для этого прямо пройти какой-то путь. Я уже полпути прошел, я знаю что такое добро, и честность с самим собой, и как поступать. Что можно по-доброму, что есть выбор как сегодня поступать: например не оценивать даже свои поступки и не мочить себя по пустякам, когда что-то не получается, а то вот раньше тоже все идеально надо было сделать, а нынче я делаю, стараюсь и получается как получается. И это тоже воля Бога - не обижаться на жизнь, а радоваться жизни и радовать других. Поэтому чтобы мне жить по законам Божьим, не надо быть кем-то, а достаточно быть самим собой.',
            ]),
        ],
        [
            'slug' => 'tolko-segodnya-vopros-057',
            'title' => 'Вопрос 057',
            'content' => implode("\n\n", [
                'Чувствуешь ли ты на себе сегодня клеймо – наркомана, алкоголика, игрока, вора, мошенника, шлюхи, неудачника, преступника, обманщика… продолжать можно долго? Если представить себе это клеймо как татуировку – что тебе комфортнее: набить поверх нее слова «воин света» (1), вытравить ее чем-то (2), ждать, пока ее сведет с тебя Бог (3), или вообще ее не трогать (4)?',
                'Когда я останавливаюсь или сбиваюсь с пути воина света, и моя одержимость берет надо мной власть тихий голосок говорит "вот ты неудачник, иди подснимись" - в такие моменты я ощущаю на себе клеймо наркомана и неудачника. Гораздо меньше стало таких моментов. Клеймо и татуировка я так понял это отметины на теле нет вен, дороги на руках, статьи 228 несколько раз, лишение прав 3 раза, как я могу изменить это? Это все останется, я не изменю прошлое, и это значит, что только Бог может помочь мне свести эту татуировку.',
            ]),
        ],
        [
            'slug' => 'tolko-segodnya-vopros-126',
            'title' => 'Вопрос 126',
            'content' => implode("\n\n", [
                'Почему равновесие в твоей жизни – это и есть здравомыслие?',
                'Это действительно прекрасный день, когда реальность перестала быть опасной и жестокой. Я вижу привычные ситуации совершенно по другому. Гармония в теле, разуме и душе. Равновесие. Freedom. Откуда я это знаю. Пришел сегодня Сашка из школы, сел со мной на кухне и рассуждает, что в 10 классе у него может быть одна из двух классных руководительниц, одна строгая, вторая меньше. А я сижу напротив, слушаю, что это или физичка или информатичка. Слушаю, а пульсом с радости, нежности и благодарности Спонсор, Богу, за всё. Для меня Сашина школа была адом всегда. Не такой как все, но должен быть как все. Прошлый год я билась как могла и не могла за то, чтобы дали аттестат. И Спонсор Богу за всё. У меня не вышло и этот год повтора девять, был не дорогой к знаниям, не дорогой сравнения себя с другими, осуждением, обвинением, а поиском уважения и контакта с Высшей Силой. Путь Воина Света.',
            ]),
        ],
        [
            'slug' => 'tolko-segodnya-vopros-125',
            'title' => 'Вопрос 125',
            'content' => implode("\n\n", [
                'Понимаешь ли ты сегодня, что такое «фильтрация мыслей через Бога» или «духовный фильтр»? Как это могло бы выглядеть в твоей жизни?',
                'На сегодня, чем больше я практикую паузу и не ведусь в моменте на одержимые мысли, желания и эмоции, тем меньше вреда наношу людям и себе.',
                'Сначала получалось редко, но чем чаще я беру паузу тем фильтр включается легче. На удивление иногда стало получаться автоматически, как дышишь. Шаги помогают наладить контакт с силой внутри меня с душой, пауза даёт возможность к ней обратиться, прислушаться к своим глубинным чувствам, к совести.',
            ]),
        ],
    ];

    foreach ($items as $index => $item) {
        pauza_seed_post('pauza_today', $item['slug'], $item['title'], $item['content'], [
            '_pauza_today_date' => 'Только сегодня',
        ], 'publish', $index + 1);
    }
}

function pauza_seed_news(): void
{
    pauza_retire_seeded_news();
}

function pauza_retire_seeded_news(): void
{
    foreach ([
        'mnpc-kak-brosit-kurit-2026',
        'mnpc-moskovskie-mastera-2026',
        'consultant-narcology-order-2026',
    ] as $slug) {
        pauza_retire_seeded_post('pauza_news', $slug);
    }
}

function pauza_retire_seeded_post(string $post_type, string $slug): void
{
    $query = new WP_Query([
        'post_type'      => $post_type,
        'name'           => $slug,
        'post_status'    => ['publish', 'pending', 'private'],
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ]);

    foreach ($query->posts as $post_id) {
        wp_update_post([
            'ID'          => (int) $post_id,
            'post_status' => 'draft',
        ]);
    }
}

function pauza_seed_sponsors(): void
{
    // Sponsor contacts are managed in WordPress admin, not seeded from theme files.
}

function pauza_seed_menu(int $home_id, int $calculator_id, int $bot_id): void
{
    $menu = wp_get_nav_menu_object('Основное меню');
    $menu_id = $menu ? (int) $menu->term_id : wp_create_nav_menu('Основное меню');
    if (is_wp_error($menu_id) || !$menu_id) {
        return;
    }

    pauza_remove_menu_items((int) $menu_id, ['Калькулятор', 'Калькуляторы', 'Материалы', 'Только сегодня', 'Бот 4 шага', 'Новости', 'Еще']);

    if ($home_id && !pauza_menu_has_item((int) $menu_id, 'Начать')) {
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title'     => 'Начать',
            'menu-item-object'    => 'page',
            'menu-item-object-id' => $home_id,
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
        ]);
    }

    $top_links = [
        ['Спонсоры', home_url('/sponsory/')],
        ['Материалы', home_url('/materialy/')],
        ['12 шагов', home_url('/12-shagov/')],
        ['Бот 4 шага', $bot_id ? get_permalink($bot_id) : home_url('/bot-4-shaga/')],
        ['Калькулятор', pauza_calculator_url()],
        ['Только сегодня', home_url('/tolko-segodnya/')],
    ];

    foreach ($top_links as $link) {
        if (pauza_menu_has_item((int) $menu_id, $link[0])) {
            continue;
        }

        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title'  => $link[0],
            'menu-item-url'    => $link[1],
            'menu-item-type'   => 'custom',
            'menu-item-status' => 'publish',
        ]);
    }

    set_theme_mod('nav_menu_locations', [
        'primary' => (int) $menu_id,
        'footer'  => (int) $menu_id,
    ]);

    update_option('pauza_menu_seeded_v4', current_time('mysql'));
}

function pauza_remove_menu_items(int $menu_id, array $titles): void
{
    $items = wp_get_nav_menu_items($menu_id);
    if (!is_array($items)) {
        return;
    }

    foreach ($items as $item) {
        if (in_array($item->title, $titles, true)) {
            wp_delete_post((int) $item->ID, true);
        }
    }
}

function pauza_menu_has_item(int $menu_id, string $title): bool
{
    $items = wp_get_nav_menu_items($menu_id);
    if (!is_array($items)) {
        return false;
    }

    foreach ($items as $item) {
        if ($title === $item->title) {
            return true;
        }
    }

    return false;
}
