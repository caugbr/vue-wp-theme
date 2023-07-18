# Grid
A simple CSS grid using SASS and flexbox

### Variables

`$grid-min-width` → Max width (.container)\
`$grid-max-width` → Min width (.container)\
`$grid-gutter` → Space between the columns\
`$grid-cols` → Number of columns\
`$breakpoints` → Breakpoints\
`$breakpoint-beyond` → Name for the last breakpoint

### Classes you can use in your row to adjust columns (*)

`no-gutter` → Remove the defined gap (`$grid-gutter`)\
`left` → Align items on left (if the number of columns is minor than  `$grid-cols`)\
`left-BP` → Align items on left only while in the breakpoint BP\
`center` → Align items on center (if the number of columns is minor than  `$grid-cols`)\
`center-BP` → Align items on center only while in the breakpoint BP\
`right` → Align items on right (if the number of columns is minor than  `$grid-cols`)\
`right-BP` → Align items on right only while in the breakpoint BP\
`between` → Space between items (if the number of columns is minor than  `$grid-cols`)\
`between-BP` → Space between items only while in the breakpoint BP\
`around` → Space around items (if the number of columns is minor than  `$grid-cols`)\
`around-BP` → Space around items only while in the breakpoint BP

### Classes you can use in your columns (*)

`col` → Create a resizable column; if you use this class for all columns, they will have equal widths\
`col-N` → Creates an element covering N columns of  `$grid-cols`\
`col-N-BP` → Creates an element covering N columns of  `$grid-cols`  only while in the breakpoint BP\
`col-0` → Hide the column\
`col-0-BP` → Hide the column only while in the breakpoint BP\
`order-ON` → Defines the property order to ON\
`order-ON-BP` → Defines the property order to ON only while in the breakpoint BP

*(\*) "N" is a number from zero to `$grid-cols`*\
*(\*) "BP" is the breakpoint name (one of the indexes on `$breakpoints` map or `$breakpoint-beyond`)*\
*(\*) "ON" is the number to set property "order"*

### Breakpoints

Our breakpoints are exclusionary. The rules set for some size will not be overwritten by the next one. Each breakpoint value sets the upper bound of a range from the last processed value (or zero for the first value). So, since it excludes any width beyond the last breakpoint, there is another breakpoint name, defined in $breakpoint-beyond which will be valid from the last defined breakpoint plus 1px. That's why we need the values in $breakpoints to be sorted in ascending order. Implicitly $breakpoints starts from zero and the added breakpoint includes every width greater than the last value.
