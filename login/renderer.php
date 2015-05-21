<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the renderers for the login pages within Moodle
 *
 * @copyright 2015 Andrew Davidson
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package calendar
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

/**
 * The primary renderer for the login pages.
 */
class core_login_renderer extends plugin_renderer_base {


    public function show_already_logged_in() {
        global $OUTPUT, $USER;

        $o = $OUTPUT->box_start();
        $logout = new single_button(new moodle_url($CFG->httpswwwroot.'/login/logout.php', array('sesskey'=>sesskey(),'loginpage'=>1)), get_string('logout'), 'post');
        $continue = new single_button(new moodle_url($CFG->httpswwwroot.'/login/index.php', array('cancel'=>1)), get_string('cancel'), 'get');
        $o .= $OUTPUT->confirm(get_string('alreadyloggedin', 'error', fullname($USER)), $logout, $continue);
        $o .= $OUTPUT->box_end();

        return $o;
    }

    /**
     * Renders the login form content
     *
     * @return string
     */
    public function show_login_form(&$mform, $show_instructions) {
        global $CFG, $OUTPUT;

        if ($show_instructions) {
            $columns = 'twocolumns';
        } else {
            $columns = 'onecolumn';
        }

        if (!empty($CFG->loginpasswordautocomplete)) {
            $autocomplete = 'autocomplete="off"';
        } else {
            $autocomplete = '';
        }
        if (empty($CFG->authloginviaemail)) {
            $strusername = get_string('username');
        } else {
            $strusername = get_string('usernameemail');
        }
        echo html_writer::start_tag('div', array('class'=>'loginbox clearfix '.$columns));
        echo html_writer::start_tag('div', array('class'=>'loginpanel'));
        if (($CFG->registerauth == 'email') || !empty($CFG->registerauth)) {
            echo html_writer::start_tag('div', array('class'=>'skiplinks'));
                echo html_writer::link(new moodle_url('/signup.php'), get_string('tocreatenewaccount'), array('class'=>'skip'));
            echo html_writer::end_tag('div');
        }
        echo $OUTPUT->heading(get_string('login'));
            echo html_writer::start_tag('div', array('class'=>'subcontent loginsub'));
                if (!empty($errormsg)) {
                    echo html_writer::start_tag('div', array('class' => 'loginerrors'));
                    echo html_writer::link('#', $errormsg, array('id' => 'loginerrormessage', 'class' => 'accesshide'));
                    echo $OUTPUT->error_text($errormsg);
                    echo html_writer::end_tag('div');
                }
                $mform->display();
                ?>
                <div class="forgetpass"><a href="forgot_password.php"><?php print_string("forgotten") ?></a></div>
                <div class="desc">
                    <?php
                        echo get_string("cookiesenabled");
                        echo $OUTPUT->help_icon('cookiesenabled');
                    ?>
                </div>
              </div>

        <?php if ($CFG->guestloginbutton and !isguestuser()) {  ?>
              <div class="subcontent guestsub">
                <div class="desc">
                  <?php print_string("someallowguest") ?>
                </div>
                <form action="index.php" method="post" id="guestlogin">
                  <div class="guestform">
                    <input type="hidden" name="username" value="guest" />
                    <input type="hidden" name="password" value="guest" />
                    <input type="submit" value="<?php print_string("loginguest") ?>" />
                  </div>
                </form>
              </div>
        <?php } ?>
             </div>
        <?php if ($show_instructions) { ?>
            <div class="signuppanel">
              <h2><?php print_string("firsttime") ?></h2>
              <div class="subcontent">
        <?php     if (is_enabled_auth('none')) { // instructions override the rest for security reasons
                      print_string("loginstepsnone");
                  } else if ($CFG->registerauth == 'email') {
                      if (!empty($CFG->auth_instructions)) {
                          echo format_text($CFG->auth_instructions);
                      } else {
                          print_string("loginsteps", "", "signup.php");
                      } ?>
                         <div class="signupform">
                           <form action="signup.php" method="get" id="signup">
                           <div><input type="submit" value="<?php print_string("startsignup") ?>" /></div>
                           </form>
                         </div>
        <?php     } else if (!empty($CFG->registerauth)) {
                      echo format_text($CFG->auth_instructions); ?>
                      <div class="signupform">
                        <form action="signup.php" method="get" id="signup">
                        <div><input type="submit" value="<?php print_string("startsignup") ?>" /></div>
                        </form>
                      </div>
        <?php     } else {
                      echo format_text($CFG->auth_instructions);
                  } ?>
              </div>
            </div>
        <?php } ?>
        <?php if (!empty($potentialidps)) { ?>
            <div class="subcontent potentialidps">
                <h6><?php print_string('potentialidps', 'auth'); ?></h6>
                <div class="potentialidplist">
        <?php foreach ($potentialidps as $idp) {
            echo  '<div class="potentialidp"><a href="' . $idp['url']->out() . '" title="' . $idp['name'] . '">' . $OUTPUT->render($idp['icon'], $idp['name']) . $idp['name'] . '</a></div>';
        } ?>
                </div>
            </div>
        <?php } ?>
        </div>
<?php
    }
}