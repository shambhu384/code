<?php
/*
* Iteration and Recursive Iteration Examples Code
*
* @link http://stackoverflow.com/questions/12077177/how-does-recursiveiteratoriterator-works-in-php
* @author hakre <http://hakre.wordpress.com>
*/
### To have these examples to work, a directory with subdirectories is needed,
### I named mine "tree":
$path = 'tree';
/**
 * Example 1: DirectoryIterator
 */
$dir = new DirectoryIterator($path);
echo "[$path]\n";
foreach ($dir as $file) {
    echo " ├ $file\n";
}
/**
 * Example 2: IteratorIterator
 */
$files = new IteratorIterator($dir);
echo "[$path]\n";
foreach ($files as $file) {
    echo " ├ $file\n";
}
/**
 * Example 3: RecursiveDirectoryIterator
 */
$dir = new RecursiveDirectoryIterator($path);
echo "[$path]\n";
foreach ($dir as $file) {
    echo " ├ $file\n";
}
/**
 * Example 4: RecursiveIteratorIterator
 */
$files = new RecursiveIteratorIterator($dir);
echo "[$path]\n";
foreach ($files as $file) {
    echo " ├ $file\n";
}
/**
 * Example 5: Meta-Information
 */
echo "[$path]\n";
foreach ($files as $file) {
    $indent = str_repeat('   ', $files->getDepth());
    echo $indent, " ├ $file\n";
}
/**
 * Example 6: Recursion Mode
 */
$dir   = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
$files = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST);
echo "[$path]\n";
foreach ($files as $file) {
    $indent = str_repeat('   ', $files->getDepth());
    echo $indent, " ├ $file\n";
}
/**
 * Appendix: Nicely Formatted Directory Listings (as used in the answer)
 */
$unicodeTreePrefix = function(RecursiveTreeIterator $tree)
{
    $prefixParts = [
        RecursiveTreeIterator::PREFIX_LEFT         => ' ',
        RecursiveTreeIterator::PREFIX_MID_HAS_NEXT => '│ ',
        RecursiveTreeIterator::PREFIX_END_HAS_NEXT => '├ ',
        RecursiveTreeIterator::PREFIX_END_LAST     => '└ '
    ];
    foreach ($prefixParts as $part => $string) {
        $tree->setPrefixPart($part, $string);
    }
};
$dir  = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_FILENAME | RecursiveDirectoryIterator::SKIP_DOTS);
$tree = new RecursiveTreeIterator($dir);
$unicodeTreePrefix($tree);
### non-recursive and recursive listing
foreach ([0, -1] as $level) {
    $tree->setMaxDepth($level);
    echo "[$path]\n";
    foreach ($tree as $filename => $line) {
        echo $tree->getPrefix(), $filename, "\n";
    }
}
/**
 * Appendix: Do It Yourself: Make the `RecursiveTreeIterator` Work Line by Line.
 */
$dir   = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
$lines = new RecursiveTreeIterator($dir);
$unicodeTreePrefix($lines);
echo "[$path]\n", implode("\n", iterator_to_array($lines));
echo "
/// Solution Suggestion ///
";
@include('recursive-directory-iterator-solution.php');
$lines = new RecursiveTreeIterator(
    new DiyRecursiveDecorator($dir)
);
$unicodeTreePrefix($lines);
echo "[$path]\n", implode("\n", iterator_to_array($lines));