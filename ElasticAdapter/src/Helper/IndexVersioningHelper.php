<?php
namespace Phalconeer\ElasticAdapter\Helper;

/**
When an index needs a new mapping:
* create a copy of the exisiting one
* make the mapping changes
* the new index name has to contain a postfix
* the postfix are fish names, each one letter further in the alphabet than the previous iteration
eg.: fnt-crichq-general-bass -> fnt-crichq-general-crucian
* first iterations of the index names does not contain the postfix, but they are treated as letter A
* all indices use the same name in the same version iteration

So far the name generations decided:
A - angler (usually not visible on the indices)
B - bass
C - crucian

For further suggestions go to https://en.wikipedia.org/wiki/List_of_common_fish_names
 */

class IndexVersioningHelper
{
    const VERSION_A = 'angler';

    const VERSION_B = 'bass';

    const VERSION_C = 'crucian';

    const VERSION_D = 'dab';
}