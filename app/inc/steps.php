<?php

const STEPS = [
    [
        'level' => 'easy',
        'title' => 'Вход разрешён. Никаких лишних вопросов.',
        'handler' => 'handleStep0',
    ],
    [
        'level' => 'easy',
        'title' => 'Снова залогинься. Теперь, обойдя полузащиту.',
        'handler' => 'handleStep1',
    ],
    [
        'level' => 'easy',
        'title' => 'Что объединяет тупые шутки и пароль? ',
        'handler' => 'handleStep2',
    ],
    [
        'level' => 'medium',
        'title' => 'Почему охранник рынка такой вежливый? Он фильтрует базар.',
        'handler' => 'handleStep3',
    ],
    [
        'level' => 'medium',
        'title' => 'Давай по порядку. Во-первых, я тебе печеньку. Во-вторых, ты мне флаг. Любой из пачки.',
        'handler' => 'handleStep4',

    ],
    [
        'level' => 'hard',
        'title' => 'Шутки конфились. Флаг в файле flag.secret.',
    ],
];

const LOGIN_FORM = <<<HTML
    <form action="" method="POST">
        <label for="login">
            <input type="text" name="login" id="login" placeholder="login" required maxlength="15">
        </label>
        <label for="pass">
            <input type="password" name="pass" id="pass" placeholder="***" required maxlength="20">
        </label>
        <button type="submit">Войти</button>
    </form>
HTML;

function handleStep0(mysqli $mysqli): array
{
    if (empty($_POST['login']) || empty($_POST['pass'])) {
        return ['solved' => false, 'content' => LOGIN_FORM];
    }
    if (strlen($_POST['login']) > 15 || strlen($_POST['pass']) > 20) {
        return [
            'solved' => false,
            'content' => LOGIN_FORM
                . "\n<div class='help-text'>Кажется, ты задумал что-то сложное. Прибереги на потом, здесь всё прямо совсем просто.</div>",
        ];
    }
    $q = "select 1 from adventurers where moniker='{$_POST['login']}' and watchword='{$_POST['pass']}'";
    try {
        $res = $mysqli->query($q);
        $r = $res->fetch_column();
    } catch (\Throwable $e) {
        return ['solved' => false, 'content' => LOGIN_FORM . "\n<div class='error'>{$e->getMessage()}</div>"];
    }
    if (!empty($r)) {
        return ['solved' => true, 'content' => null];
    }
    return [
        'solved' => false,
        'content' => LOGIN_FORM . "\n<div class='help-text'>Нет такого пользователя</div>",
    ];
}

function handleStep1(mysqli $mysqli): array
{
    if (empty($_POST['login']) || empty($_POST['pass'])) {
        return ['solved' => false, 'content' => LOGIN_FORM];
    }
    if (strlen($_POST['login']) > 15 || strlen($_POST['pass']) > 20) {
        return [
            'solved' => false,
            'content' => LOGIN_FORM
                . "\n<div class='help-text'>Кажется, ты задумал что-то сложное. Прибереги на потом, здесь всё прямо совсем просто.</div>",
        ];
    }
    $q = "select 1 from adventurers where moniker='{$_POST['login']}' and watchword=?";
    try {
        $stmt = $mysqli->prepare($q);
        $stmt->bind_param('s', $_POST['pass']);
        $stmt->execute();
        $r = $stmt->get_result()?->fetch_column();
    } catch (\Throwable $e) {
        return ['solved' => false, 'content' => LOGIN_FORM . "\n<div class='error'>{$e->getMessage()}</div>"];
    }
    if (!empty($r)) {
        return ['solved' => true, 'content' => null];
    }
    return [
        'solved' => false,
        'content' => LOGIN_FORM . "\n<div class='help-text'>Нет такого пользователя</div>",
    ];
}

function handleStep2(mysqli $mysqli): array
{
    $action = empty($_GET['a']) ? 'jokes' : $_GET['a'];
    $loginActiveClass = ($action === 'login') ? 'active' : '';
    $jokesActiveClass = ($action === 'jokes') ? 'active' : '';
    $links = <<<HTML
        <section class="links">
            <a href="/?s=2&a=login" class="$loginActiveClass">Вход</a>
            <a href="/?s=2&a=jokes" class="$jokesActiveClass">Шуточки</a>
        </section>
HTML;

    if ($action === 'login') {
        if (empty($_POST['login']) || empty($_POST['pass'])) {
            return ['solved' => false, 'content' => "$links\n" . LOGIN_FORM];
        }
        $q = "select 1 from adventurers where moniker=? and watchword=?";
        try {
            $stmt = $mysqli->prepare($q);
            $stmt->bind_param('ss', $_POST['login'], $_POST['pass']);
            $stmt->execute();
            $r = $stmt->get_result()?->fetch_column();
        } catch (\Throwable $e) {
            return [
                'solved' => false,
                'content' => "$links\n" . LOGIN_FORM . "\n<div class='error'>Какая-то ошибка.</div>",
            ];
        }
        if (!empty($r)) {
            return ['solved' => true, 'content' => null];
        }
        return [
            'solved' => false,
            'content' => "$links\n" . LOGIN_FORM . "\n<div class='help-text'>Нет такого пользователя</div>",
        ];
    }
    if ($action === 'jokes') {
        if (!empty($_GET['id'])) {
            $q = "select * from flat_jokes where id={$_GET['id']}";
            try {
                $res = $mysqli->query($q);
                $r = $res->fetch_assoc();
            } catch (\Throwable $e) {
                return [
                    'solved' => false,
                    'content' => "$links\n" . "\n<div class='error'>Какая-то ошибка</div>",
                ];
            }
            if (empty($r)) {
                return [
                    'solved' => false,
                    'content' => "$links\n<div class='help-text'>Эту шутку еще не придумали.</div>",
                ];
            }
            $joke = <<<HTML
                <section class="joke">
                    <div class="intro">
                        {$r['intro']}
                    </div>
                    <div class="punch">
                        {$r['punch']}
                    </div>
                    <img src="/img/rip_bro.gif" alt="ухахака">
                </section>
HTML;

            return [
                'solved' => false,
                'content' => "$links\n$joke",
            ];
        }
        $offset = random_int(1, 4);
        $q = "select id, intro from flat_jokes limit 3 offset $offset";
        try {
            $mysqli->real_query($q);
            $rows = $mysqli->use_result();
        } catch (\Throwable $e) {
            return [
                'solved' => false,
                'content' => "$links\n" . LOGIN_FORM . "\n<div class='error'>{$e->getMessage()}</div>",
            ];
        }
        $table = "";
        foreach ($rows as $row) {
            $table .= <<<HTML
                <div class="intro"><a href="/?s=2&a=jokes&id={$row['id']}">{$row['intro']}</a></div>            
HTML;
        }
        return [
            'solved' => false,
            'content' => "$links\n<section class='jokes'>$table</section>",
        ];
    }
    header("Location: /?s=2&a=jokes", true, 301);
    exit();
}

const HARD_BANNED = [
    'and',
    'or',
    'create',
    'insert',
    'update',
    'set',
    'char',
    'ascii',
    'cast',
    'convert',
    'if',
    'concat',
    'unhex',
    'grant',
    'alter',
    'begin',
    'between',
    'case',
    'user',
    'database',
    'version',
    'sleep',
    'declare',
    'limit',
    'execute',
    'plugin',
    'extractvalue',
    'group',
    'regexp',
    'file',
    'like',
    'rlike',
    'row_number',
    'xor',
    'delete',
    'truncate',
    'flush',
    'mid',
    'substr',
    'substring',
    'show',
    'information',
    'schema',
    'from_base64',
    'as',
    '/',
    '\\',
    '-',
    ',',
    '.',
    ';',
    '!',
    ' ',
    '+',
    '|',
    '&',
    '0x',
];
const SOFT_BANNED = [
    'SELECT',
    'select',
    'FROM',
    'from',
    'JOIN',
    'join',
    'UNION',
    'union',
];

function handleStep3(mysqli $mysqli): array
{
    if (!isset($_ENV['FE_FLAG_FILE']) || !is_file($_ENV['FE_FLAG_FILE']) || !is_readable($_ENV['FE_FLAG_FILE'])) {
        exit('Уровень не настроен');
    }
    $action = empty($_GET['a']) ? 'validate' : $_GET['a'];
    $validateActiveClass = ($action === 'validate') ? 'active' : '';
    $filteredActiveClass = ($action === 'filtered') ? 'active' : '';
    $links = <<<HTML
        <section class="links">
            <a href="/?s=3&a=validate" class="$validateActiveClass">Проверка</a>
            <a href="/?s=3&a=filtered" class="$filteredActiveClass">Запрещёнка</a>
        </section>
HTML;
    if ($action === 'validate') {
        $form = <<<HTML
            <form action="" method="post">
                <label for="flag">
                    <input type="text" name="flag" id="flag" placeholder="предъявите секретик">
                </label>
                <button type="submit">Предъявить</button>
            </form>
            <div class="help-text">А лежит он в таблице с запрещёнкой</div>
HTML;
        if (!empty($_POST['flag']) && trim(file_get_contents($_ENV['FE_FLAG_FILE'])) === $_POST['flag']) {
            return ['solved' => true, 'content' => null];
        }
        return [
            'solved' => false,
            'content' => "$links\n$form",
        ];
    }
    if ($action === 'filtered') {
        $form = <<<HTML
            <p>Список внизу неполный. Можешь воспользоваться формой, чтобы проверить нужные ключевые слова.</p>
            <p>Ах, да. Они же зпрещены... Ну, разберешься.</p> 
            <br>
            <form action="" method="get">
                <label for="term">
                    <input type="text" name="term" id="term">
                </label>
                <input type="hidden" name="s" value="3">
                <input type="hidden" name="a" value="filtered">
                <button type="submit">Поищем...</button>
            </form>
            <br>   
HTML;
        $term = $_GET['term'] ?? '';
        $searched = '';
        if (!empty($term)) {
            $searched = '<p>Искал: ' . htmlspecialchars($term, ENT_QUOTES | ENT_HTML5) . ' </p>';
            $c = 0;
            str_replace(HARD_BANNED, '', mb_strtolower(urldecode($term)), $c);
            if ($c > 0) {
                return [
                    'solved' => false,
                    'content' => "$links\n$form\n$searched\n<img src='/img/cook.jpg' alt='По тонкому льду!'/>",
                ];
            }
            str_replace(SOFT_BANNED, '', $term, $c);
            if ($c > 0) {
                return [
                    'solved' => false,
                    'content' => "$links\n$form\n$searched\n<img src='/img/cook.jpg' alt='По тонкому льду!'/>",
                ];
            }
            $q = "select id, keyword from filtered where keyword like '$term%' limit 10";
        } else {
            $q = 'select id, keyword from filtered limit 10';
        }

        try {
            $mysqli->real_query($q);
            $rows = $mysqli->use_result();
        } catch (\Throwable $e) {
            return [
                'solved' => false,
                'content' => "$links\n$form\n$searched\n<div class='error'>Какая-то ошибка</div>",
            ];
        }
        $table = "";
        foreach ($rows as $row) {
            $table .= <<<HTML
                <tr><td>{$row['keyword']}</td></tr>            
HTML;
        }
        return [
            'solved' => false,
            'content' => "$links\n$form\n$searched\n<table class='filtered'>$table</table>",
        ];
    }
    header("Location: /?s=3&a=validate", true, 301);
    exit();
}

function handleStep4(mysqli $mysqli): array
{
    //real_escape_string
    return ['solved' => false, 'content' => ''];
    if (!isset($_COOKIE['not_suspicious'])) {
        $cookieVal = uniqid();
        $flag = 'flag[' . md5(rand()) . ']';
        $q = "insert into cookies (val, flag) values ('$cookieVal', '$flag')";
        try {
            $mysqli->query($q);
        } catch (\Throwable $e) {
            exit('Уровень почему-то не работает: ' . $e->getMessage());
        }
        setcookie('not_suspicious', $cookieVal);
    }


    return [
        'solved' => false,
        'content' => 'asdad',
    ];
}




