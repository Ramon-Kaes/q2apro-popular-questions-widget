<?php
/*
    Plugin Name: q2apro Popular questions widget
    Plugin URI: http://www.q2apro.com/plugins/popular-questions
    Plugin Description: Displays the most viewed questions in a widget
    Plugin Version: 0.3
    Plugin Date: 2023-10-10
    Plugin Author: q2apro
    Plugin Author URI: http://www.q2apro.com/
    Plugin License: GPLv3
    Plugin Minimum Question2Answer Version: 1.5
    Plugin Update Check URI: 

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    More about this license: http://www.gnu.org/licenses/gpl.html
    
    Extended by: Ramon Kaes (https://github.com/Ramon-Kaes)
    
    Changes:
    - Optimized for PHP 8.3
    - Added user avatars to the widget
    - Default avatar SVG added for users without an avatar
*/

if (!defined('QA_VERSION')) {
    header('Location: ../../');
    exit;
}

// language file
qa_register_plugin_phrases('q2apro-popular-questions-lang-*.php', 'q2apro_popularqu_lang');

// widget
qa_register_plugin_module('widget', 'q2apro-popular-questions-widget.php', 'q2apro_popular_questions_widget', 'q2apro Popular Questions Widget');

// admin
qa_register_plugin_module('module', 'q2apro-popular-questions-admin.php', 'q2apro_popular_questions_admin', 'Popular Questions Admin');

function q2apro_save_most_viewed_questions()
{
    // save checktime of cache
    qa_opt('q2apro_popularqu_checktime', time());

    $maxquestions = qa_opt('q2apro_popularqu_maxqu');
    $lastdays = qa_opt('q2apro_popularqu_lastdays');
    $ourTopQuestions = qa_db_read_all_assoc(
        qa_db_query_sub(
            'SELECT postid, title, acount, userid FROM ^posts 
            WHERE created > NOW() - INTERVAL # DAY
            AND type = "Q"
            AND closedbyid IS NULL
            ORDER BY views DESC
            LIMIT #;',
            $lastdays,
            $maxquestions
        )
    );

    $saveoutput = '';
    foreach ($ourTopQuestions as $qu) {
        $activity_url = qa_path_html(qa_q_request($qu['postid'], $qu['title']), null, qa_opt('site_url'), null, null);
        $questionlink = '<a href="' . $activity_url . '">' . htmlspecialchars($qu['title']) . '</a>';
        $answercnt = '';
        if (qa_opt('q2apro_popularqu_answercount')) {
            $acnttitle = ($qu['acount'] == 1) ? qa_lang('q2apro_popularqu_lang/answer_one') : $qu['acount'] . ' ' . qa_lang('q2apro_popularqu_lang/answers');
            $answercnt = '<span title="' . $acnttitle . '">(' . $qu['acount'] . ')</span>';
        }
        $userinfo = qa_db_select_with_pending(qa_db_user_account_selectspec($qu['userid'], true));
        $avatar_url = qa_get_user_avatar_url(
            $userinfo['flags'],
            $userinfo['email'],
            $userinfo['avatarblobid'],
            40,
            true
        );
        if (empty($avatar_url)) {
            $avatar_url = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(dirname(__FILE__) . '/default-avatar.svg'));
        }
        $avatar = '<img src="' . $avatar_url . '" class="qa-avatar-image" height="40" width="40" alt="User Avatar">';
        $saveoutput .= '<li>
                            ' . $avatar . ' ' . $questionlink . ' ' . $answercnt . '
                        </li>';
    }

    // save into cache
    qa_opt('q2apro_popularqu_cached', $saveoutput);
}

/*
    Omit PHP closing tag to help avoid accidental output
*/