<?php

namespace ActiveCollabToJiraMigrator\Util;

use League\HTMLToMarkdown\HtmlConverter;
use ActiveCollabToJiraMigrator\Process\UserMapper;

/**
 * Utility functions for issues.
 */
class Markup {

  /**
   * Convert HTML Markup to Markdown.
   *
   * @param string $markup
   *
   * @return string
   *   The resulting markdown.
   */
  public static function toMarkdown(string $markup) {
    $converter = new HtmlConverter();
    return $converter->convert($markup);
  }

  /**
   * Convert HTML Markup to Jira Wiki Syntax.
   *
   * @param string $markup
   * @param UserMapper $userMapper The userMapper object. Some functionalities are not executed if empty.
   *
   * @return string
   *   The resulting Jira Wiki Syntax.
   */
  public static function toJiraWikiSyntax(string $markup, UserMapper $userMapper = NULL) {
    // Convert to markdown to solve some things already and make it easier:
    $text = self::toMarkdown($markup);

    // Code ``` to {code}:
    $text = \str_replace('```', '{code}', $text);

    if (!empty($userMapper)) {
      // Mentions: <span class="mention">Full name</span> to [~username].
      $text = preg_replace_callback("/<span class=\"mention\"[^>]*>(.*?)<\/span>/is", function ($match) use ($userMapper) {
        $displayName = $match[1];
        if (!empty($displayName)) {
          $acEmail = $userMapper->mapAcDisplayNameToAcEmail($displayName);
          if (!empty($acEmail)) {
            // Replace by email:
            $displayName = $acEmail;
          }
        }
        return '[~' . $displayName . ']';
      }, $text);
    }

    return $text;
  }

}
