<?php
  /**
   * Usage:
   * githubCsvTools exported.csv --token=[Use yours] --organization=thinkshout --repository=therepo
   */
  $file = fopen('zip/sample 1bec89ed077841438bee9362f3e85781.csv', 'r');
  $labels = array_flip([
    "Component Name",
    "Task/Feature ",
    "Task/Feature Summary",
    "Task cost",
    "Priority",
    "Goal achieved",
    "Property"
  ]);
  $milestones = array_flip([
    'None',
    'Base CMS',
    'Enhanced CMS',
    'Engagement',
    'User Login, Profiles, & Permissions',
    'Home Page',
    'Landing Page',
    'Organization',
    'Program',
    'Reusable Block Types',
    'Global Search',
    'Migration'
  ]);
  $long_goals = [
    '1 User-centered content strategy and experience',
    '2. More cohesive and accessible and successful digital platform for the organization’s administrators.',
    '3. Defined and measurable success in bridging the human-digital aspect.',
    '4. Stay known as an established connection hub.'
  ];
  $short_goals = [
    '1. User experience',
    '2. Admin experience',
    '3. Measurable engagement',
    '4. Maintain reputation'
  ];
  $region = 'en_US';
  $currency = 'USD';
  $formatter = new NumberFormatter($region, NumberFormatter::CURRENCY);
  $write_file = fopen('exported.csv', 'w');

  $generic_ticket = '## Dev [x hrs]
  - [ ] TODO.

## Theming [x hrs]
- [ ] Complete the [Accessibility Testing Checklist](https://github.com/thinkshout/Front-End-Style-Guide/wiki/5.-Testing-tools#accessibility-testing-checklist)

## Documentation
- [ ] Document this in the [Content Editor\'s guide](https://docs.google.com/document/d/1GHdi7oHtBYfyqT_ih4Ga7bRjUAAXYsvT2sT8JPRTIMI/edit)

## QA [x hrs]
To test this piece of functionality, follow these steps:
1. As a [user role], go to [url](url), set [form item] to [value], click submit.
2. You should see the message [what user should see].';

  $output = '';
  $line_numb = 0;
  while (($line = fgetcsv($file)) !== FALSE) {
    if ($line_numb == 0) {
      $output .= '"milestone","title","body","labels"' . PHP_EOL;
    }
    else {
      // Calculate the hours
      $cost = $formatter->parse($line[$labels["Task cost"]]);
      $hours = $cost / 185;

      if (isset($milestones[$line[$labels["Component Name"]]]) && $line[$labels["Priority"]] == 'Required') {
        $output .= '"' . $milestones[$line[$labels["Component Name"]]];

        $output .= '","[' . $hours . "hrs] " . str_replace('"', '', $line[$labels["Task/Feature "]]);

        // Get the full body
        $clean = str_replace(["/ ","/"], [" ", " "], $line[$labels["Task/Feature "]]);
        $clean = str_replace('"', "", $clean);
        $clean = str_replace('.', " ", $clean);
        $clean = str_replace('(', "\(", $clean);
        $clean = str_replace(')', "\)", $clean);
        $clean = substr($clean,0,50);
        $feature_pattern = '~^' . $clean . '.*\.md$~';
        $additional_body = $line[$labels["Task/Feature Summary"]];

        // Find a file that starts with the task's "Task/Feature " value.
        $related_file = preg_grep($feature_pattern, scandir("/Users/mariafisher/Sites/github-import/zip/files"));
        if (!empty($related_file)) {
          $file_string = "/Users/mariafisher/Sites/github-import/zip/files/" . current($related_file);
          $file_contents = fopen($file_string, "r");
          $additional_body = fread($file_contents, filesize($file_string));
          // Link to the correct file urls
          $additional_body = str_replace('.png](', '.png](https://github.com/mariacha/github-import/wiki/notion-imgs/', $additional_body);
          $additional_body = str_replace('"', '“', $additional_body);
          fclose($file_contents);
        }
        else {
          echo "could not find file starting with " . $line[$labels["Task/Feature "]] . '<br>';
        }

        $output .= '","' . $additional_body . PHP_EOL . $generic_ticket;

        $output .= '","' . str_replace($long_goals, $short_goals, $line[$labels["Goal achieved"]]) . '"' . PHP_EOL;
      }
    }
    $line_numb++;

  }
  fclose($file);
  fwrite($write_file, $output);
  fclose($write_file);
