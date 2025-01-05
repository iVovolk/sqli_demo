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
        'title' => 'Футки кончились. Флаг в файле flag.secret.',
        'handler' => 'handleStep5',
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
            if (str_contains(mb_strtolower($_GET['id']), 'cookie')) {
                return [
                    'solved' => false,
                    'content' => "$links\n<img src='/img/cook.jpg' alt='По тонкому льду!'/>",
                ];
            }
            $q = "select * from flat_jokes where id={$_GET['id']}";
            try {
                $res = $mysqli->query($q);
                $r = $res->fetch_assoc();
            } catch (\Throwable $e) {
                return [
                    'solved' => false,
                    'content' => "$links\n<div class='error'>Какая-то ошибка</div>",
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
    'cookies',
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
            <p>Ах, да. Они же зпрещены... Ну, разберёшься.</p> 
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
            'content' => "$links\n$form\n$searched\n<table class='filtered striped'>$table</table>",
        ];
    }
    header("Location: /?s=3&a=validate", true, 301);
    exit();
}

const FLAG_FORM = <<<HTML
    <form action="" method="post">
        <label for="flag">
            <input type="text" name="flag" id="flag" placeholder="флаг сюда">
        </label>
        <button type="submit">Предъявить</button>
    </form>
HTML;

function handleStep4(mysqli $mysqli): array
{
    if (!isset($_COOKIE['not_suspicious'])) {
        $cookieVal = uniqid('', true);
        $flag = 'flag[' . md5(mt_rand()) . ']';
        $q = "insert into cookies (val, flag) values ('$cookieVal', '$flag')";
        try {
            $mysqli->query($q);
        } catch (\Throwable $e) {
            exit('Уровень почему-то не работает: ' . $e->getMessage());
        }
        setcookie('not_suspicious', $cookieVal);
    }
    $action = empty($_GET['a']) ? 'validate' : $_GET['a'];
    $validateActiveClass = ($action === 'validate') ? 'active' : '';
    $logsActiveClass = ($action === 'logs') ? 'active' : '';
    $links = <<<HTML
        <section class="links">
            <a href="/?s=4&a=validate" class="$validateActiveClass">Проверка</a>
            <a href="/?s=4&a=logs" class="$logsActiveClass">Логи</a>
        </section>
HTML;
    if ($action === 'validate') {
        if (!empty($_POST['flag'])) {
            $logSql = "insert into logs (ip, cookie, flag) values (?, ?, ?)";
            $cookie = str_ireplace('create', '🗿', $_COOKIE['not_suspicious']);
            try {
                $logStmt = $mysqli->prepare($logSql);
                $logStmt->bind_param('sss', $_SERVER['REMOTE_ADDR'], $cookie, $_POST['flag']);
                $logStmt->execute();
            } catch (\Throwable $e) {
                //don't care
            }
            $checkSql = "select flag from cookies where flag = ? limit 1";
            try {
                $stmt = $mysqli->prepare($checkSql);
                $stmt->bind_param('s', $_POST['flag']);
                $stmt->execute();
                $r = $stmt->get_result()?->fetch_column();
            } catch (\Throwable $e) {
                return [
                    'solved' => false,
                    'content' => "$links\n" . FLAG_FORM . "\n<div class='error'>Какая-то ошибка</div>",
                ];
            }
            if (!empty($r)) {
                return ['solved' => true, 'content' => null];
            }
        }
        return [
            'solved' => false,
            'content' => "$links\n" . FLAG_FORM,
        ];
    }

    if ($action === 'logs') {
        $logsSql = "select * from logs order by id desc";
        try {
            $mysqli->real_query($logsSql);
            $rows = $mysqli->use_result();
        } catch (\Throwable $e) {
            return [
                'solved' => false,
                'content' => "$links\n<div class='error'>Какая-то ошибка</div>",
            ];
        }
        $stripes = "";
        foreach ($rows as $row) {
            $cookie = htmlspecialchars($row['cookie'], ENT_QUOTES | ENT_HTML5);
            if (str_contains(mb_strtolower($row['flag']), 'flag')) {
                $flag = '***тут был флаг***';
            } else {
                $flag = htmlspecialchars($row['flag'], ENT_QUOTES | ENT_HTML5);
            }
            $ip = htmlspecialchars($row['ip'], ENT_QUOTES | ENT_HTML5);
            $id = $row['id'];
            $stripes .= <<<HTML
                <tr>
                    <td>$ip</td>
                    <td>$cookie</td>
                    <td>$flag</td>
                    <td>
                        <form action="/?s=4&a=del" method="post">
                            <input type="hidden" name="id" value="$id">
                            <button type="submit">❌</button>
                        </form>
                    </td>
                </tr>            
HTML;
        }
        $table = <<<HTML
            <p>Система записывает все твои попытки и грязные трюки.</p>
            <p>Но мы же не в интернете: из этих логов, при желании, можно удалить навсегда.</p>
            <table class="striped logs">
                <thead>
                    <tr><th>IP</th><th>Печенька</th><th>Может флаг</th><th>Удалить</th></tr>
                </thead>
                <tbody>$stripes</tbody>
            </table>
HTML;
        return [
            'solved' => false,
            'content' => "$links\n$table",
        ];
    }

    if ($action === 'del') {
        $id = (int)$_POST['id'];
        $c = $mysqli->query("select cookie from logs where id = $id")->fetch_column();
        $sql = "delete from logs where cookie = '$c'";
        try {
            $mysqli->multi_query($sql);
            do {
                $mysqli->store_result();
            } while ($mysqli->more_results() && $mysqli->next_result());
        } catch (\Throwable $e) {
            return [
                'solved' => false,
                'content' => "$links\n<div class='error'>Упс, не получилось удалить.</div>",
            ];
        }
        header("Location: /?s=4&a=logs", true, 301);
        exit();
    }

    header("Location: /?s=4&a=validate", true, 301);
    exit();
}

function handleStep5(mysqli $mysqli): array
{
    $flagFile = '../web/flag.secret';
    if (!is_file($flagFile) || !is_readable($flagFile)) {
        exit('Уровень не настроен');
    }
    if (!empty($_POST['flag']) && trim(file_get_contents($flagFile)) === $_POST['flag']) {
        return ['solved' => true, 'content' => null];
    }
    return [
        'solved' => false,
        'content' => FLAG_FORM,
    ];
}
