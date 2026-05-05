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
        'Пауза работает',
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
    pauza_seed_news();
    pauza_seed_sponsors();
    pauza_seed_menu($home_id, $calculator_id, $bot_id);

    update_option('pauza_seeded_v1', current_time('mysql'));
}
add_action('after_switch_theme', 'pauza_seed_default_content', 20);
add_action('after_switch_theme', 'pauza_seed_step_full_texts_for_existing_posts', 30);
add_action('after_switch_theme', 'pauza_seed_step_structured_blocks_for_existing_posts', 31);
add_action('admin_init', 'pauza_seed_step_full_texts_for_existing_posts');
add_action('admin_init', 'pauza_seed_step_structured_blocks_for_existing_posts');
add_action('admin_init', 'pauza_ensure_news_seeded');
add_action('admin_init', 'pauza_ensure_default_menu_items');

function pauza_ensure_news_seeded(): void
{
    if (get_option('pauza_news_seeded_v3')) {
        return;
    }

    pauza_seed_news();
    update_option('pauza_news_seeded_v3', current_time('mysql'));
}

function pauza_ensure_default_menu_items(): void
{
    if (get_option('pauza_menu_seeded_v3')) {
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
    if (get_option('pauza_full_text_seeded_v1')) {
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
        if ($number && isset($full_texts[$number]) && !pauza_meta((int) $post_id, '_pauza_step_full_text')) {
            update_post_meta((int) $post_id, '_pauza_step_full_text', $full_texts[$number]);
        }
    }

    update_option('pauza_full_text_seeded_v1', current_time('mysql'));
}

function pauza_seed_step_structured_blocks_for_existing_posts(): void
{
    if (get_option('pauza_step_blocks_seeded_v2')) {
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

        if (!pauza_meta((int) $post_id, '_pauza_step_materials')) {
            update_post_meta((int) $post_id, '_pauza_step_materials', implode("\n", $blocks[$number]['materials']));
        }

        if (!pauza_meta((int) $post_id, '_pauza_step_exercises')) {
            update_post_meta((int) $post_id, '_pauza_step_exercises', $blocks[$number]['exercises']);
        }
    }

    update_option('pauza_step_blocks_seeded_v2', current_time('mysql'));
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
        'calculator_instruction_url' => '',
        'calculator_telegram_url'    => '',
        'calculator_max_url'         => '',
        'calculator_intro'           => 'Калькуляторы открываются как внешние боты в Telegram или MAX. На сайте мы только объясняем, когда ими пользоваться и куда отправлять результат.',
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
            'title' => 'Первый шаг',
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
            'title' => 'Второй шаг',
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
            'title' => 'Третий шаг',
            'status' => 'Перед 4 шагом',
            'goal' => 'Научиться чаще брать паузу и передавать решение Богу своего понимания.',
            'content' => 'Третий шаг закрепляет базовые понятия, молитвы и глоссарий, чтобы человек был готов к глубокой письменной работе четвертого шага.',
            'requirements' => [
                'Выбран спонсор или проводник.',
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
            'title' => 'Четвертый шаг',
            'status' => 'Внешний бот',
            'goal' => 'Выполнить глубокую письменную инвентаризацию в отдельном инструменте.',
            'content' => 'В MVP сайт не переносит работу четвертого шага внутрь WordPress. Он объясняет точку перехода и ведет в Telegram-бот, где находится рабочий функционал.',
            'requirements' => [
                'Третий шаг завершен.',
                'Есть спонсор или проводник для сопровождения.',
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
            'title' => 'Пятый шаг',
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
            'title' => 'Шестой шаг',
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
            'title' => 'Седьмой шаг',
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
            'title' => 'Восьмой шаг',
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
            'title' => 'Девятый шаг',
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
            'title' => 'Десятый шаг',
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
            'title' => 'Одиннадцатый шаг',
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
            'title' => 'Двенадцатый шаг',
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
    return [
        '1' => [
            'materials' => [
                'Вводное видео INTRO.',
                'Инструкция к калькулятору выздоровления.',
                'Видео 003-010 по первому шагу.',
                'Группа 1 шага в Telegram и MAX.',
            ],
            'exercises' => "Топ-5 зависимостей: главная зависимость, курение и еще три зависимости.\n\nВарианты зависимостей из документа: порно, кофеин, еда, контроль, созависимость, гнев, схемы, игры, сплетни, ничегонеделание, антидепрессанты, транжирство, диеты, соцсети, секс, новости, кредиты, ставки, успех, трудоголизм.\n\nПять вопросов по зависимости: что говорит сделать голова; беру ли паузу и наношу ли вред; как не вижу интересов других; как вру другим и себе; какая цифра получается на калькуляторе.",
        ],
        '2' => [
            'materials' => [
                'День 9-18: видео 011-020 по второму шагу смотреть по порядку.',
                'Группа 2 шага в Telegram и MAX используется для вопросов по текущему шагу.',
                'Длинный текст про похоть и отношения оставлен во вкладке "Текст руководителя" для проверки большого prose-блока.',
            ],
            'exercises' => "Ответить по каждой из пяти зависимостей: какими станут мои отношения в свободе от этой зависимости.\n\nПодвести итоги и зачитать работу спонсору.",
        ],
        '3' => [
            'materials' => [
                'Видео 021-030 по третьему шагу.',
                'Группа 3 шага в Telegram и MAX.',
                'Переход в Telegram-бот 4 шага после завершения.',
            ],
            'exercises' => "Текст третьего шага.\n\nВолшебные слова: короткая фраза для возвращения к паузе после ошибки.\n\nУпражнение на знание глоссария: бессилие, зависимость, одержимость, компульсивность, эгоцентризм, отрицание, жизнь, неуправляемость, здравомыслие, могущественная сила, вред, Бог, воля Бога, выздоровление, исцеление, препоручение, пауза, молитва.",
        ],
        '4' => [
            'materials' => [
                'Telegram-бот 4 шага: внешний инструмент для письменной инвентаризации.',
                'Страница сайта только объясняет момент перехода и не хранит личные ответы.',
                'Группы проекта и каналы не заменяют бот: бот используется именно на четвертом шаге.',
            ],
            'exercises' => "Рабочие вопросы и ответы выполняются во внешнем боте. Сайт не должен хранить личные ответы четвертого шага.",
        ],
        '5' => [
            'materials' => [
                'Работа со спонсором после четвертого шага.',
                'На сайте остается короткая навигационная страница.',
            ],
            'exercises' => "Прочитать работу по четвертому шагу спонсору и согласовать переход к шестому шагу.",
        ],
        '6' => [
            'materials' => [
                'Видео шестого шага.',
                'Группа 6 шага в Telegram и MAX.',
            ],
            'exercises' => "Работа с дефектами из финального текста пятого шага.\n\nПять вопросов по каждому дефекту: как проявляется; чего заставляет бояться; как будет выглядеть жизнь без него; готов ли я, чтобы Бог избавил; что делаю дальше.",
        ],
        '7' => [
            'materials' => [
                'Видео седьмого шага.',
                'Группа 7 шага в Telegram и MAX.',
            ],
            'exercises' => "Утро: написать молитву своими словами и попросить сил не совершать действия под дефектом.\n\nВечер: написать опыт дня по дефекту.\n\nСвязки дефектов и принципов из документа остаются в полном тексте шага.",
        ],
        '8' => [
            'materials' => [
                'День 8 шага: смотреть видео восьмого шага по порядку, не переносить их в общую свалку материалов.',
                'Группа 8 шага в Telegram и MAX: вопросы по текущей работе и спискам вреда.',
                'Ссылка ЦБ для курса валют при материальном ущербе: https://www.cbr.ru/currency_base/daily/',
                'Переход в 9 шаг показывается только после завершения списков и согласования со спонсором.',
            ],
            'exercises' => "Списки вреда из документа:\nфизический себе;\nфизический другим;\nкосвенный физический;\nпсихический себе;\nпсихический другим;\nматериальный организациям;\nматериальный людям;\nматериальный друзьям и партнерам;\nматериальный родным.\n\nДля материального ущерба отдельно фиксируется сумма, дата, курс валюты и понятное объяснение, как считался вред.\n\nУпражнение ВДА остается внутри восьмого шага. Его не нужно выносить в общий раздел материалов: человек открывает вкладку шага, читает инструкцию и работает со спонсором.\n\nПосле завершения восьмого шага человек переходит к девятому шагу: письма и план выхода согласуются со спонсором.",
        ],
        '9' => [
            'materials' => [
                'Группа 9 шага в Telegram и MAX.',
            ],
            'exercises' => "Письмо каждому человеку из списка восьмого шага.\n\nПлан выхода на человека согласуется со спонсором.\n\nГрафик встреч, звонков, посещений кладбищ или прочтения письма Богу. График материальных возмещений отдельно.",
        ],
        '10' => [
            'materials' => [
                'Группа 10 шага в Telegram и MAX.',
            ],
            'exercises' => "Каждый вечер выбрать одну главную ситуацию дня.\n\nРазобрать по 4 шагу: был ли вред и какой дефект.\n\nРазобрать по 9 шагу: возместить ущерб до конца дня, если был.\n\nРазобрать по 7 шагу: какие дефекты и страхи видны, на какие принципы просить заменить.",
        ],
        '11' => [
            'materials' => [
                'Группа 11 шага в Telegram и MAX.',
                'Спикерские на канале.',
            ],
            'exercises' => "Слушать спикерские по одной в день.\n\nЕжедневно просить знать волю Бога и силы ее исполнить.\n\nПосле прослушивания ответить на вопросы о религии, понимании Бога, молитве, медитации и воле Бога.",
        ],
        '12' => [
            'materials' => [
                'Группа 12 шага в Telegram и MAX.',
            ],
            'exercises' => "Писать в группе только слова или действия, прямо связанные с несением вести и работой с подспонсорными, учениками, поднаставными и реабилитационными центрами.",
        ],
    ];
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
            'slug' => 'calculator-telegram-bot',
            'title' => 'Калькулятор в Telegram',
            'content' => 'Внешний бот-калькулятор. Сайт не хранит ответы и не встраивает калькулятор в админку.',
            'type' => 'calculator',
            'url' => '',
            'label' => 'Открыть Telegram-бот',
        ],
        [
            'slug' => 'calculator-max-bot',
            'title' => 'Калькулятор в MAX',
            'content' => 'Внешний MAX-инструмент для ежедневного расчета, когда ссылка будет подтверждена владельцем.',
            'type' => 'calculator',
            'url' => '',
            'label' => 'Открыть MAX-бот',
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
    pauza_seed_post(
        'pauza_today',
        'tolko-segodnya-start',
        'Только сегодня: стартовый текст',
        'Тексты этого раздела редактируются владельцем сайта. Здесь можно публиковать ежедневные или периодические обращения, не смешивая их с инструкциями по шагам.',
        ['_pauza_today_date' => 'Стартовый текст'],
        'publish'
    );
}

function pauza_seed_news(): void
{
    $news = [
        [
            'slug' => 'mnpc-kak-brosit-kurit-2026',
            'title' => 'Как бросить курить сигареты и электронки: советы врача и работающие методики',
            'content' => 'Внешняя тематическая новость для проверки формата раздела. Это не материал программы и не инструкция руководителя проекта.',
            'type' => 'Внешняя тестовая новость',
            'source' => 'МНПЦ наркологии, 27 апреля 2026',
            'origin' => 'external_test',
            'url' => 'https://narcologos.ru/news/?year=2026',
            'label' => 'Открыть источник',
        ],
        [
            'slug' => 'mnpc-moskovskie-mastera-2026',
            'title' => 'Специалист МНПЦ наркологии представил Московскую наркологию на конкурсе «Московские мастера 2026»',
            'content' => 'Внешняя тематическая новость для проверки формата карточек: дата, источник, короткий текст и кнопка.',
            'type' => 'Внешняя тестовая новость',
            'source' => 'МНПЦ наркологии, 24 апреля 2026',
            'origin' => 'external_test',
            'url' => 'https://narcologos.ru/news/?year=2026',
            'label' => 'Открыть источник',
        ],
        [
            'slug' => 'consultant-narcology-order-2026',
            'title' => 'Новый порядок медпомощи по профилю «психиатрия-наркология» вступит в силу 1 сентября 2026 года',
            'content' => 'Внешняя юридическая новость для проверки формата. Она не является материалом программы и должна быть визуально отделена от шагов.',
            'type' => 'Внешняя тестовая новость',
            'source' => 'КонсультантПлюс, 12 января 2026',
            'origin' => 'external_test',
            'url' => 'https://www.consultant.ru/legalnews/30513/',
            'label' => 'Открыть источник',
        ],
    ];

    foreach ($news as $item) {
        pauza_seed_post('pauza_news', $item['slug'], $item['title'], $item['content'], [
            '_pauza_news_type'         => $item['type'],
            '_pauza_news_origin'       => $item['origin'],
            '_pauza_news_source'       => $item['source'],
            '_pauza_news_url'          => $item['url'],
            '_pauza_news_button_label' => $item['label'],
        ], 'publish');
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

    pauza_remove_menu_items((int) $menu_id, ['Калькулятор', 'Калькуляторы', 'Материалы', 'Только сегодня', 'Бот 4 шага', 'Еще']);

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
        ['Новости', home_url('/novosti/')],
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

    $more_id = wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title'  => 'Еще',
        'menu-item-url'    => '#',
        'menu-item-type'   => 'custom',
        'menu-item-status' => 'publish',
    ]);

    if (!is_wp_error($more_id) && $more_id) {
        $secondary_links = [
            ['Только сегодня', home_url('/tolko-segodnya/')],
            ['Калькуляторы', $calculator_id ? get_permalink($calculator_id) : home_url('/calculator/')],
        ];

        foreach ($secondary_links as $link) {
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title'     => $link[0],
                'menu-item-url'       => $link[1],
                'menu-item-type'      => 'custom',
                'menu-item-status'    => 'publish',
                'menu-item-parent-id' => (int) $more_id,
            ]);
        }
    }

    set_theme_mod('nav_menu_locations', [
        'primary' => (int) $menu_id,
        'footer'  => (int) $menu_id,
    ]);

    update_option('pauza_menu_seeded_v3', current_time('mysql'));
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
