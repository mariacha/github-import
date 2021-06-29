## What this is
A php script that takes the output of a Notion export in the style (Markdown & CSV) and manipulates it
so that each notion card/page becomes a ticket in Github.

## How to use it
1. Start with a Notion board of cards, and export using the instructions at https://presstige.io/p/Export-a-page-as-Markdown-69b6031dd9454022abed8e23a86b0e1e
2. Unzip the exported result and replace the "zip" folder with the unzipped exported file. You should now see
something looking like this:
```
  /zip
  /zip/my notion name 123abchash.csv
  /zip/my notion name 123abchash/my card name abc456hash.md
  /zip/my notion name 123abchash/my card name abc456hash.md
  /zip/my notion name 123abchash/my card name abc456hash/my-uploaded-image.png
```
3. Update the paths in the script. There are quite a few that are hard-coded.
4. Visit the make-import.php script in your browser. This will update the contents of `exported.csv`
5. If you have images associated with your Notion cards, you can commit them to the repo, or to the repo's wiki. The script currently assumes you've done the latter, in a folder called "notion-imgs" . Example: `https://github.com/mariacha/github-import/wiki/notion-imgs/`
6. Use `githubCsvTools exported.csv --token=[Use yours] --organization=yourname --repository=therepo` to create all the issues in exported.csv. It's recommended you test this on a test repo first (one you can delete when you're done).