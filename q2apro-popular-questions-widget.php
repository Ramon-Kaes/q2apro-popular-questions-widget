<?php
/*
	Plugin Name: q2apro Popular questions widget
	Plugin Author: q2apro
*/

class q2apro_popular_questions_widget
{
	
	function allow_template($template)
	{
		$allow=false;
		
		switch ($template)
		{
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
				$allow=true;
				break;
		}
		
		return $allow;
	}
	
	function allow_region($region)
	{
		$allow=false;
		
		switch ($region)
		{
			case 'side':
				$allow=true;
				break;
			case 'main':
			case 'full':					
				break;
		}
		
		return $allow;
	}

	function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
	{
		$today = date('Y-m-d');
		if($today != qa_opt('q2apro_popularqu_checkdate'))
		{
			qa_opt('q2apro_popularqu_checkdate', $today);
			
			q2apro_save_most_viewed_questions();
		}

		// output widget using cached values
		$output = '';
		$output .= '
			<div class="popularquestions-widget">
				<p>
					'.qa_lang('q2apro_popularqu_lang/most_popqu').' '.qa_opt('q2apro_popularqu_lastdays').' '.qa_lang('q2apro_popularqu_lang/days').'
				</p>
				<ol>
					'.qa_opt('q2apro_popularqu_cached').'
				</ol>
			</div>
		';
		
		// css (could also go into your theme css to save load)
		$output .= '
			<style type="text/css">
				.popularquestions-widget {
					margin:30px 0 30px 0;
				}
				.popularquestions-widget p {
					font-size:14px;
				}
				.popularquestions-widget ol {
					padding:0 10px 0 15px;
					font-size:12px;
				}
				.popularquestions-widget ol li {
					padding:5px 0;
				}
				.popularquestions-widget ol li a {
					color:#05B;
					word-wrap:break-word;
				}
				.popularquestions-widget ol li span {
					cursor:default;color:#555;
				}
			</style>
		';
	
		// output widget into theme
		$themeobject->output($output);
		
	 } // end output_widget

} // end class q2apro_popular_questions_days_widget

/*
	Omit PHP closing tag to help avoid accidental output
*/