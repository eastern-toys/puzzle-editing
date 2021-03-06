<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "utils.php";

function echoNav($isselected, $href, $linktext, $condition) {
    if ($condition) {
        $navclass = $isselected ? "selnav" : "nav";
        echo "<li class='nav'><a class='$navclass' href='$href'>$linktext</a></li>\n";
    }
}

function echoNav1($selnav, $name, $linktext, $condition) {
    echoNav($selnav == $name, $name . ".php", $linktext, $condition);
}

function fullTitle() {
    return 'MH' . HUNT_YEAR . ' puzzletron authoring server (' . (DEVMODE ? 'test/dev' : (PRACMODE ? 'practice' : 'actual mystery hunt-writing')) . ' instance)';
}

function head($selnav = "", $title = -1) {
    if ($title == -1) {$title = fullTitle();}
$hunt = mktime(12, 00, 00, 1, HUNT_DOM, HUNT_YEAR);
$now = time();
$timediff = abs($hunt-$now);
$days = (int)($timediff/(60 * 60 * 24));
$hrs = (int)($timediff/(60 * 60))-(24*$days);
$mins = (int)($timediff/(60))-(24*60*$days)-(60*$hrs);
$cdmsg = "";
if ($now > $hunt) {
    $cdmsg = "after hunt started!!!";
    $cdclass = "cunum";
} else {
    $cdmsg = "left until hunt.";
    $cdclass = "cdnum";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" type="image/vnd.microsoft.icon" href="favicon.ico" />

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/reset-min.css" />
    <link rel="stylesheet" type="text/css" href="css/base-min.css" />
    <link rel="stylesheet" type="text/css" href="css/fonts-min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <?php if ($selnav == "people" || $selnav == "account") { ?> <link rel="stylesheet" type="text/css" href="css/profiles.css" /> <?php } ?>
    <title><?php echo $title; ?></title>
    <script type='text/javascript' src='jquery-1.4.2.js'></script>
    <script type='text/javascript' src='jquery.tablesorter.min.js'></script>
    <script type="text/javascript" src='js.js'></script>
    <script type="text/javascript">
    function openMenu() {
      var x = document.getElementById("navbar");
      if (x.className === "menubar") {
        x.className += " open";
      } else {
        x.className = "menubar";
      }
    }
    </script>
</head>
<body>
<div id="container">
    <div id="header">
        <div id="titlediv">
            <h1><?php echo fullTitle(); ?></h1>
        </div>
        <div id="navbar" class="menubar">
	    <div id="nav-icon"><a href="javascript:void(0);" onclick="openMenu()">&#x2261;</a></div>
            <ul class="nav">
<?php if (!empty(WIKI_URL)) { ?>
                <li class="nav"><a class="nav wikinav" target="_blank" href="<?php echo WIKI_URL; ?> ">Wiki</a></li>
<?php }

echoNav($selnav == "home", "index.php", "Home", true);

if (isset($_SESSION['uid'])) {
    $suid = $_SESSION['uid'];
    echoNav1($selnav, "people",         "People",              true);
    echoNav1($selnav, "admin",          "Admin",               hasServerAdminPermission($suid));
    echoNav1($selnav, "author",         "Author",              true);
    echoNav1($selnav, "roundcaptain",   "Round Captain",       (USING_ROUND_CAPTAINS) && hasRoundCaptainPermission($suid));
    echoNav1($selnav, "spoiled",        "Spoiled",             true);
    echoNav1($selnav, "editor",         "Discussion Editor",   hasEditorPermission($suid));
    echoNav1($selnav, "approver",       "Approval Editor",     (USING_APPROVERS) && (hasApproverPermission($suid) || isEditorChief($suid)));
    echoNav1($selnav, "testsolving",    "Testsolving",         true);
//    echoNav1($selnav, "factcheck",      "Fact Check",          true);
    echoNav1($selnav, "ffc",            "Final Fact Check",    hasFactCheckerPermission($suid));
    echoNav1($selnav, "editorlist",     "Editor List",         isEditorChief($suid) || hasServerAdminPermission($suid));
    echoNav1($selnav, "testadmin",      "Testing Admin",       hasTestAdminPermission($suid));
    echoNav1($selnav, "testsolveteams", "TS Team Assignments", (USING_TESTSOLVE_TEAMS) && hasTestAdminPermission($suid));
    echoNav1($selnav, "answers",        "Answers",             canChangeAnswers($suid));
    echoNav1($selnav, "allpuzzles",     "All Puzzles",         canSeeAllPuzzles($suid));
    echoNav1($selnav, "editor-pick-special",     "Puzzles Needing Help",         hasEditorPermission($suid));
}
?>
            </ul>
        </div>
        <div id="top">
            <div id="countdowndiv">
                <span id="countdown">
                <?php
                    if ($days !== 0) {
                        $daypl = $days === 1 ? "" : "s";
                        echo "<span class=\"$cdclass\">$days</span> day$daypl<span class=\"cdhidden\">, ";
                    }
                    $hrpl =  $hrs  === 1 ? "" : "s";
                    echo "<span class=\"$cdclass\">$hrs</span> hour$hrpl and ";
                    $minpl = $mins === 1 ? "" : "s";
                    echo "<span class=\"$cdclass\">$mins</span> minute$minpl</span> $cdmsg";
                ?>
                </span>
            </div>
            <div id="logindiv">
<?php if (isset($_SESSION['uid'])) {
    echo 'Logged in as <strong>' . getUserUsername($_SESSION['uid']) . '</strong>';
    echo '<a href="account.php"' . ($selnav == "account" ? ' class="accsel"' : "") . '>Your Account</a>';
    if (MAILING_LISTS) { echo '<a href="mailinglists.php"' . ($selnav == "mailinglists" ? ' class="accsel"' : "") . '>Mailing Lists</a>'; }
    if (!TRUST_REMOTE_USER) { echo '<a href="logout.php">Logout</a>'; }
} else { ?>
                <span class="notloggedin">Not logged in</span> <a href="login.php">Login</a>
        <?php } ?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div id="body">
<?php
}

function foot() {
?>
    </div>
    <div id="footer">
        <hr />
        <p>
        This is the website for the hunt writing team.

        For technical assistance, please contact the <a href="mailto:<?php echo HELP_EMAIL; ?>">Server Administrators</a>.<br/>

        This software is available <a href="http://github.com/mysteryhunt/puzzle-editing/" target="_blank">on GitHub</a> under the Simplified BSD license.<br/>
        The copyrights for the puzzles and comments contained herein are retained by the puzzle authors.</p>
    </div>
</div>
</body>
</html>

<?php
}

function printPerson($p) {
    $id = $p['uid'];
    $uname = $p['username'];
    $picture = $p['picture'];
    $fullname = $p['fullname'];
    $email = $p['email'];

    if (strncmp($uname, "test", 4) == 0) {
        // Ignore test users.
        return;
    }

    $pic = "<img src=\"nophoto.gif\" />";
    if ($picture != "") {
        if (USING_AWS) {
            $picsrc = AWS_ENDPOINT . AWS_BUCKET . "/uploads/pictures/thumbs/$id.jpg";
            $pic = "<img src=\"".$picsrc."\" />";
        } else {
            $picsrc = "uploads/pictures/thumbs/$id.jpg";
            if (file_exists($picsrc)) {
                $pic = "<img src=\"".$picsrc."\" />";
            }
        }
    }

    $roleNames = getUserRolesAsList($id);
    if (canSeeAllPuzzles($id)) {
        $profclass = "seeallprofilebox";
    } elseif (hasApproverPermission($id)) {
        $profclass = "approverprofilebox";
    } elseif ($roleNames) {
        $profclass = "specprofilebox";
    } else {
        $profclass = "profilebox";
    }
?>
    <div class="<?php echo $profclass; ?>">
        <div class="profileimg"><?php echo $pic ?></div>
        <div class="profiletxt">
            <span class="profilename"><?php echo "$fullname"; ?> (<?php echo "$uname"; ?>)</span>
            <span class="profiletitle"><?php echo $roleNames; ?></span>
            <span class="profilecontact"><a href="mailto:<?php echo $email ?>"><?php echo $email ?></a></span>
<?php
    $sql = "SELECT * FROM user_info_keys";
    $result = get_rows($sql);
    foreach ($result as $r) {
        $shortname = $r['shortname'];
        $longname = $r['longname'];
        $user_key_id = $r['id'];
        $sql = sprintf("SELECT value FROM user_info_values WHERE person_id = '%s' AND user_info_key_id = '%s'",
            mysql_real_escape_string($id), mysql_real_escape_string($user_key_id));
        $res = get_rows($sql);
        if (count($res) > 0 && $res[0]['value'] != "") {
?>
            <span class="profilesect"><?php echo "<b>$longname</b>: " . $res[0]['value']; ?></span>
<?php
        }
    }
?>
        </div>
        <div class="profilefooter"></div>
    </div>
<?php
}

function displayCell($colname, $contents, $secure = FALSE) {
    $classes = "";
    if ($secure) { $classes .= "secure"; }
    if (empty($contents)) { $classes .= " empty"; }
    $classes = trim($classes);
    if (empty($classes)) {
        echo "<td data-col='$colname'>$contents</td>";
    } else {
        echo "<td data-col='$colname' class='$classes'>$contents</td>";
    }
}

function displayQueue($uid, $puzzles, $fields, $test, $filter = array(), $addLinkArgs = "", $hidedeadpuzzles = TRUE) {
    $fields = explode(" ", $fields);
    $showNotes = in_array("notes", $fields);
    $showAnswer = in_array("answer", $fields);
    $showSummary = in_array("summary", $fields);
    $showEditorNotes = in_array("editornotes", $fields);
    $showTags = in_array("tags", $fields);
    $showAuthorsAndEditors = in_array("authorsandeditors", $fields);
    $showNumTesters = in_array("numtesters", $fields);
    $showTesters = in_array("testers", $fields);
    $showCurrentPuzzleTesterCount = in_array("currentpuzzletestercount", $fields);
    $showFinalLinks = in_array("finallinks", $fields);
    $showFunAndDifficulty = in_array("funanddifficulty", $fields);
    if (!$puzzles) {
        echo "<span class='emptylist'>No puzzles to list</span><br/>";
        return;
    }
    $statuses = getPuzzleStatuses();
    $statusSort = get_assoc_array("SELECT id, ord FROM pstatus", "id", "ord");

    $deadstatusid = getDeadStatusId();
    $flaggedPuzzles = getFlaggedPuzzles($uid);
?>
    <table class="puzzidea tablesorter">
    <thead>
        <tr>
            <th>ID</th>
            <?php if (USING_CODENAMES) {echo '<th>Codename</th>';} ?>
            <th>Title</th>
            <th>Puzzle Status</th>
            <th>Round(s)</th>
            <?php if ($showSummary) {echo '<th>Summary</th>';} ?>
            <?php if ($showEditorNotes) {echo '<th>Editor Notes</th>';} ?>
            <?php if ($showTags) {echo '<th>Tags</th>';} ?>
            <?php if ($showNotes) {echo '<th>Status Notes</th>';} ?>
            <?php if ($showNotes) {echo '<th>Runtime Info</th>';} ?>
            <?php if ($showNotes) {echo '<th>Priority</th>';} ?>
            <?php if ($showFunAndDifficulty) {echo '<th>Median Fun</th>';} ?>
            <?php if ($showFunAndDifficulty) {echo '<th>Median Difficulty</th>';} ?>
            <?php if ($showAnswer) {echo '<th>Answer</th>';} ?>
            <?php if (!$test) { echo '<th>Last Commenter</th>';} ?>
            <?php if (!$test) { echo '<th>Last Comment</th>';}?>
            <?php if (!$test){ echo '<th>Last Status Change</th>';}?>
            <?php if ($showAuthorsAndEditors) {echo '<th>Authors</th>';} ?>
            <?php if ($showAuthorsAndEditors) {echo '<th>Discussion Editors</th>';} ?>
            <?php if (MIN_EDITORS >= 0 && $showAuthorsAndEditors) {echo '<th>D.Eds Needed</th>';} ?>
            <?php if (USING_APPROVERS && $showAuthorsAndEditors) {echo '<th>Approval Editors</th>';} ?>
            <?php if ($showAuthorsAndEditors) {echo '<th>Approvals</th>';} ?>
            <?php if ($showNumTesters) {echo '<th># Testers</th>';} ?>
            <?php if ($showCurrentPuzzleTesterCount) {echo '<th># Current Testers</th>';} ?>
            <?php if ($showTesters) {echo '<th>Testers</th>';} ?>
            <?php if ($showTesters) {echo '<th>Last Test Report</th>';} ?>
            <?php if (($showTesters) && (USING_TESTSOLVE_REQUESTS)) {echo '<th>Testsolve requests</th>';} ?>
            <?php if ($showFinalLinks) {echo '<th>Final Links</th>';} ?>
        </tr>
    </thead>
    <tbody>
<?php
    foreach ($puzzles as $pid) {
        $puzzleInfo = getPuzzleInfo($pid);
        $tags = getTagsAsList($pid);
        $roundDict = getRoundDictForPuzzle($pid);
        $rounds = array_map(function($r) {return $r['rid'];}, $roundDict);
        // This is totally the wrong way to do this. The right way involves
        // writing SQL.
        if ($filter) {
            if ($filter[0] == "status" && $filter[1] != $puzzleInfo["pstatus"]) {
                continue;
            }
            if ($filter[0] == "author" && !isAuthorOnPuzzle($filter[1], $pid)) {
                continue;
            }
            if ($filter[0] == "editor" && !isEditorOnPuzzle($filter[1], $pid)) {
                continue;
            }
            if ($filter[0] == "approver" && !isApproverOnPuzzle($filter[1], $pid)) {
                continue;
            }
            if ($filter[0] == "tag" && !isTagOnPuzzle($filter[1], $pid)) {
                continue;
            }
            if ($filter[0] == "round" && !in_array($filter[1], $rounds)) {
                continue;
            }
            if ($filter[0] != "status" && $hidedeadpuzzles && $puzzleInfo["pstatus"] == $deadstatusid) {
                continue;
            }
        }
        elseif ($hidedeadpuzzles && $puzzleInfo["pstatus"] == $deadstatusid) {
            continue;
        }

        $title = $puzzleInfo["title"];
        if ($title == NULL) {
            $title = '(untitled)';
        }
        $codename = getCodename($pid);
        $lastComment = getLastCommentDate($pid);
        $lastCommenter = getLastCommenter($pid);
        $lastVisit = getLastVisit($uid, $pid);
        $flagged = in_array($pid, $flaggedPuzzles);
        $status = $puzzleInfo["pstatus"];

        $puzzclass="puzz";
        if (($lastVisit == NULL || strtotime($lastVisit) < strtotime($lastComment)) || $test) {
          $puzzclass = "puzz-new";
        } elseif ($flagged) {
          $puzzclass = "puzz-flag";
        }
        echo "<tr class='$puzzclass'>";

        if ($test) {
	    displayCell('id', "<a href='test.php?pid=$pid$addLinkArgs'>$pid</a>");
        } else {
	    displayCell('id', "<a href='puzzle.php?pid=$pid$addLinkArgs'>$pid</a>");
        }
?>
        <?php if (USING_CODENAMES) { displayCell('codename', $codename); } ?>
	<?php displayCell('title', $title); ?>
        <td data-col='status' data-sort-value='<?php echo $statusSort[$status] ?>'><?php echo $statuses[$status]; ?></td>
        <?php displayCell('round', implode(', ', array_map(function($r) {return $r['name'];}, $roundDict))); ?>
        <?php if ($showSummary) { displayCell('summary', $puzzleInfo["summary"], TRUE);} ?>
        <?php if ($showEditorNotes) {displayCell('editornotes', $puzzleInfo["editor_notes"], TRUE);} ?>
        <?php if ($showTags) {displayCell('tags', $tags);} ?>
        <?php if ($showNotes) {displayCell('notes', $puzzleInfo["notes"]);} ?>
        <?php if ($showNotes) {displayCell('runtime', $puzzleInfo["runtime_info"]);} ?>
        <?php if ($showNotes) {displayCell('priority', getPriorityWord($puzzleInfo["priority"]));} ?>
        <?php if ($showFunAndDifficulty) {displayCell('medianfun', getMedianFeedback($pid, 'fun'));} ?>
        <?php if ($showFunAndDifficulty) {displayCell('mediandifficulty', getMedianFeedback($pid, 'difficulty'));} ?>
<?php
        if ($showAnswer) {
	    displayCell('answer', getAnswersForPuzzleAsList($pid), getAnswersForPuzzleAsList($pid) != "");
        } ?>
        <?php if (!$test) {displayCell('commenter', $lastCommenter);} ?>
        <?php if (!$test) {displayCell('comment', $lastComment);} ?>
        <?php if (!$test) {displayCell('statuschange', getLastStatusChangeDate($pid));} ?>
        <?php if ($showAuthorsAndEditors) {displayCell('authors', getAuthorsAsList($pid));} ?>
        <?php if ($showAuthorsAndEditors) {
            $est = getEditorStatus($pid);
	    displayCell('editors', $est[0]);
            if (MIN_EDITORS >= 0) {
	      displayCell('editorsneeded', $est[1]);
            }
        } ?>
        <?php if (USING_APPROVERS && $showAuthorsAndEditors) {displayCell('approvers', getApproversAsList($pid));} ?>
        <?php if ($showAuthorsAndEditors) {displayCell('approvals', countPuzzApprovals($pid));} ?>
        <?php if ($showNumTesters) {displayCell('numtesters', getNumTesters($pid));} ?>
        <?php if ($showCurrentPuzzleTesterCount) {displayCell('numcurtesters', getCurrentPuzzleTesterCount($pid));} ?>
        <?php if ($showTesters) {displayCell('testers', getCurrentTestersAsList($pid));} ?>
        <?php if ($showTesters) {displayCell('lasttest', getLastTestReportDate($pid));} ?>
        <?php if (($showTesters) && (USING_TESTSOLVE_REQUESTS)) {displayCell('testsolvereqs', getTestsolveRequestsForPuzzle($pid));} ?>
        <?php if ($showFinalLinks) {displayCell('finallinks', "<a href='" .  getBetaLink($title) . "' target='_blank'>beta</a> <a href='". getFinalLink($title)."' target='_blank'>final</a>");} ?>

    </tr>
<?php
    }
?>
    </tbody>
    </table>
<?php
}

// Make groups of checkboxes
// Takes an associative array and the name of the form element
function makeOptionElements($toDisplay, $name, $highlightKey = NULL) {
    if (!$toDisplay) {
        echo '<em>(none)</em>';
        return;
    }

    $maxLength = 5;
    $maxCol = 10;

    // Figure out how many columns are necessary to maintain max length
    // Use maxCol to keep from having too many columns
    $numCol = min(ceil(count(array_keys($toDisplay))/$maxLength), $maxCol);

    $i = 1;
    echo '<table>';
    foreach ($toDisplay as $key => $value) {
        if ($key == NULL) {
            continue;
        }
        // Start a new row, if necessary
        if (($i % $numCol) == 1) {
            echo '<tr>';
        }
        // Add answer information
        if ($key == $highlightKey) {
            echo "<td class='highlightkey'>";
        } else {
            echo '<td>';
        }
        echo "<label><input type='checkbox' name='$name" . "[]' value='$key' /> $value</label>";
        echo '</td>';

        // End row, if number of columns reached
        if (($i % $numCol) == 0) {
            echo '</tr>';
        }
        $i++;
    }

    // Close last row, if necessary
    if (($i % $numCol) != 1) {
        echo '</tr>';
    }
    echo '</table>';
}

function displayPuzzleStats($uid) {
    $max_rows = 6;

    $totalNumberOfPuzzles = countLivePuzzles();
    $numberOfEditors = getNumberOfEditorsOnPuzzles("discuss");
    $moreThanThree = $totalNumberOfPuzzles - $numberOfEditors['0'] - $numberOfEditors['1'] - $numberOfEditors['2'] - $numberOfEditors['3'];

    $numberOfApprovalEditors = getNumberOfEditorsOnPuzzles("approval");
    $moreThanThreeApproval = $totalNumberOfPuzzles - $numberOfApprovalEditors['0'] - $numberOfApprovalEditors['1'] - $numberOfApprovalEditors['2'] - $numberOfApprovalEditors['3'];

    $userNumbers = getNumberOfPuzzlesForUser($uid);

    $editor = $userNumbers['editor'];

    $tester = $userNumbers['currentTester'];
    if ($userNumbers['doneTester'] > 0) {
        $tester .= ' (+' . $userNumbers['doneTester'] . ' done)';
    }
?>
<div class="puzzle-stats-section">
  <div class="puzzle-stats-row">
   <div class="puzz-stats stats-block">
       <table>
           <tr>
               <th class="puzz-stats" colspan="2"><?php echo $totalNumberOfPuzzles; ?> Total Live Puzzles/Ideas</th>
           </tr>
           <tr>
               <td class="puzz-stats">You Are Discuss Ed</td>
               <td class="puzz-stats"><?php echo $editor; ?></td>
           </tr>
           <tr>
               <td class="puzz-stats">You Are Approve Ed</td>
               <td class="puzz-stats"><?php echo $userNumbers['approver']; ?></td>
           </tr>
           <tr>
               <td class="puzz-stats">You Are Author</td>
               <td class="puzz-stats"><?php echo $userNumbers['author']; ?></td>
           </tr>
           <tr>
               <td class="puzz-stats">You Are Spoiled</td>
               <td class="puzz-stats"><?php echo $userNumbers['spoiled']; ?></td>
           </tr>
           <tr>
               <td class="puzz-stats">You Are Tester</td>
               <td class="puzz-stats"><?php echo $tester; ?></td>
           </tr>
           <!--<tr>
               <td class="puzz-stats">Available To Edit</td>
               <td class="puzz-stats"><?php echo $userNumbers['available']; ?></td>
           </tr>-->
       </table>
   </div>
   <div class="discussion-ed-stats stats-block">
       <table>
           <tr>
               <th class="discussion-ed-stats" colspan="2">Discuss Eds</th>
           </tr>
           <tr>
               <td class="discussion-ed-stats">Zero</td>
               <td class="discussion-ed-stats"><?php echo $numberOfEditors['0']; ?></td>
           </tr>
           <tr>
               <td class="discussion-ed-stats">One</td>
               <td class="discussion-ed-stats"><?php echo $numberOfEditors['1']; ?></td>
           </tr>
           <tr>
               <td class="discussion-ed-stats">Two</td>
               <td class="discussion-ed-stats"><?php echo $numberOfEditors['2']; ?></td>
           </tr>
           <tr>
               <td class="discussion-ed-stats">Three</td>
               <td class="discussion-ed-stats"><?php echo $numberOfEditors['3']; ?></td>
           </tr>
           <tr>
               <td class="discussion-ed-stats">&gt;Three</td>
               <td class="discussion-ed-stats"><?php echo $moreThanThree; ?></td>
           </tr>
       </table>
   </div>
<?php
    if (USING_APPROVERS) {
?>
   <div class="approval-ed-stats stats-block">
       <table>
           <tr>
               <th class="approval-ed-stats" colspan="2">Approval Eds</th>
           </tr>
           <tr>
               <td class="approval-ed-stats">Zero</td>
               <td class="approval-ed-stats"><?php echo $numberOfApprovalEditors['0']; ?></td>
           </tr>
           <tr>
               <td class="approval-ed-stats">One</td>
               <td class="approval-ed-stats"><?php echo $numberOfApprovalEditors['1']; ?></td>
           </tr>
           <tr>
               <td class="approval-ed-stats">Two</td>
               <td class="approval-ed-stats"><?php echo $numberOfApprovalEditors['2']; ?></td>
           </tr>
           <tr>
               <td class="approval-ed-stats">Three</td>
               <td class="approval-ed-stats"><?php echo $numberOfApprovalEditors['3']; ?></td>
           </tr>
           <tr>
               <td class="approval-ed-stats">&gt;Three</td>
               <td class="approval-ed-stats"><?php echo $moreThanThreeApproval; ?></td>
           </tr>
       </table>
   </div>
<?php
    }

    $puzzleStatuses = getPuzzleStatuses();
    $pstatusCol = ceil(count($puzzleStatuses) / $max_rows) * 2;

    $statuses = NULL;
    $statusCounts = getPuzzleStatusCounts();
    foreach ($puzzleStatuses as $sid => $name) {
        $count = (array_key_exists($sid, $statusCounts) ? $statusCounts[$sid] : 0);
        $status = NULL;
        $status['id'] = $sid;
        $status['name'] = $name;
        $status['count'] = $count;
        $statuses[] = $status;
    }
?>
    <div class="p-stats stats-block">
        <table>
            <tr>
                <th class="p-stats" colspan="<?php echo $pstatusCol; ?>">Puzzle Status</th>
            </tr>
<?php
    for ($row = 0; $row < $max_rows; $row++) {
        for ($col = 0; $col < ($pstatusCol / 2); $col++) {
            $n = $row + ($col * $max_rows);

            if ($col==0) {
                echo '
                <tr>';
            }
            if ($n >= count($puzzleStatuses)) {
                echo '
                    <td></td>';
                echo '
                    <td></td>';
            } else {
                $num = $statuses[$n];
                $name = $num['name'];
                $count = $num['count'];

                echo '
                    <td class="p-stats">' . $name . '</td>';
                echo '
                    <td class="p-stats">' . $count . '</td>';
            }

            if ($col == ($pstatusCol/2 - 1)) {
                echo '
                    </tr>';
            }
        }
    }
?>
        </table>
    </div>
    <div class="answer-stats stats-block">
        <table>
            <tr>
                <th class="answer-stats" colspan="2"> Answer Status</th>
            </tr>
            <tr>
                <td class="answer-stats"> Total Answers </td>
                <td class="answer-stats"> <?php echo numAnswers(); ?> </td>
            </tr>
            <tr>
                <td class="answer-stats"> Assigned </td>
                <td class="answer-stats"> <?php echo answersAssigned(); ?> </td>
            </tr>
            <tr>
                <td class="answer-stats"> Unassigned </td>
                <td class="answer-stats"> <?php echo (numAnswers() - answersAssigned()); ?> </td>
            </tr>
        </table>
    </div>
  </div>
</div>
<?php
}
