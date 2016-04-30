Roles
=====

Bit mask
--------
Roles are defined by bit-masks in the class `Fairpay\Bundle\UserBundle\Security\Acl\MaskBuilder`. 
Hierarchy is defined by the mask itself, each role is made of its own bit and all the bits of the roles it includes.

Their are vendor specific roles, so a user car have a set of roles regarding a specific vendor and a completely different set of roles regarding another.
Those vendor specific roles are stores in the lower bits of the mask.

Their are also global roles that apply to every vendor when a user has one. Those are stored in the upper bits of the mask.

How are roles stored
--------------------
Roles for a user are stored in the `$permissions` attribute of the User entity and mapped to the `roles` column in mySQL.
They are store in an array where each key is the vendor id and the value is the mask.
To make access control faster all the global roles are stores in a mask as well under the `global` key.

Groups
------
To easily manage roles we use groups. Each vendor has its own set of groups that he can customized.
A group is simply a name, a mask, and a list of users. A user can only be in one group per vendor.
To add or remove a user from a group simply add / remove it from the group using the appropriate methods and persist the group entity.