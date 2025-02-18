<?php
/*
    Plugin Name: q2apro Popular questions widget
    Plugin Author: q2apro (extended by Ramon Kaes)
*/

class q2apro_popular_questions_widget
{
    function allow_template($template)
    {
        $allow = false;

        switch ($template) {
            case 'activity':
            case 'qa':
            case 'questions':
            case 'hot':
            case 'ask':
            case 'categories':
            case 'question':
            case 'tag':
            case 'tags':
            case 'unanswered':
            case 'user':
            case 'users':
            case 'search':
            case 'admin':
            case 'custom':
                $allow = true;
                break;
        }

        return $allow;
    }

    function allow_region($region)
    {
        $allow = false;

        switch ($region) {
            case 'side':
                $allow = true;
                break;
            case 'main':
            case 'full':
                break;
        }

        return $allow;
    }

    function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
    {
        $checktime = (int) qa_opt('q2apro_popularqu_checktime');
        $checkhours = (int) qa_opt('q2apro_popularqu_checkhours');
        
        if (time() - $checktime > $checkhours * 60 * 60) {
            q2apro_save_most_viewed_questions();
        }

        // output widget using cached values
        $output = '';
        $output .= '
            <div class="popularquestions-widget">
                <h2>
                    ' . qa_lang('q2apro_popularqu_lang/most_popqu') . ' ' . qa_opt('q2apro_popularqu_lastdays') . ' ' . qa_lang('q2apro_popularqu_lang/days') . '
                </h2>
                <ol>
                    ' . qa_opt('q2apro_popularqu_cached') . '
                </ol>
            </div>
        ';

        // css (could also go into your theme css to save load)
        $output .= '
            <style type="text/css">
                .popularquestions-widget {
                    margin: 0;
                }
                .popularquestions-widget p {
                    
                }
                .popularquestions-widget ol {
                    padding:0;
                    font-size: .75rem;
                }
                .popularquestions-widget ol li {
                    padding: 5px 0;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }
                .popularquestions-widget ol li img.qa-avatar-image {
                    margin-right: 10px;
                    width: 36px;
                    height: 36px;
                }
                .popularquestions-widget ol li a {
                    word-wrap: break-word;
                    flex-grow: 1;
                    margin-right: 10px;
                    overflow: hidden;
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical; 
                }
                .popularquestions-widget ol li span {
                    white-space: nowrap;
                    cursor: default;
                }
            </style>
        ';

        // output widget into theme
        $themeobject->output($output);
    }
}

/*
    Omit PHP closing tag to help avoid accidental output
*/