Versionning
===========

This file will document the versionning process of the PHP library
PronounceableWord.

Version number
==============

The version number will follow the X.Y.Z format. The following table describes
the meaning of incrementation for each number:

======== =============================================================================================
Version  Description
======== =============================================================================================
#.0.0    Modification impacting on the public usage
0.#.0    Modification not impacting on the public usage
0.0.#    Small modification not impacting on the public usage, like bug fixes and adding of new tests
======== =============================================================================================

Branching model
===============

The PronounceableWord project uses Git to its full potential, following
the advice of this article:
http://nvie.com/posts/a-successful-git-branching-model/ .

================== =============== ================================= ====================================
Repository/Branch  Pull from       Merge to                          Description
================== =============== ================================= ====================================
origin/master                                                        Stable branch, actual release.
origin/develop     origin/master   origin/master                     Stable branch, for next release.
x/hotfix-*         origin/master   origin/master and origin/develop  Bug fixes branch for origin/master.
x/x                origin/develop  origin/develop                    Feature branch.
================== =============== ================================= ====================================

Tags
----

Tags are created from the origin/master branch, incrementing the version number.

x/hotfix-* branches
-------------------

The repository is named x because it can be from another repository (through
forks), and the branch is called hotfix-* because it is suffixed by "hotfix-"
and named after the feature it will fix.

x/x branches
------------

The repository is named x because it can be from another repository (through
forks), and the branch is called x because it is named after the feature it
will implement.
