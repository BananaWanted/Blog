# Test Publishing with 3rd Editor Markdown Formatting
[iA Writer](https://ia.net/writer)
[Byword](https://bywordapp.com/)
[Ulysses](https://ulysses.app/)
macOS editions only

# Headings
# H1 Heading ABCD abcd 1234 ?:'";,.!&
text
## H2 Heading ABCD abcd 1234 ?:'";,.!&
text
### H3 Heading ABCD abcd 1234 ?:'";,.!&
text
#### H4 Heading ABCD abcd 1234 ?:'";,.!&
text
##### H5 Heading ABCD abcd 1234 ?:'";,.!&
text
###### H6 Heading ABCD abcd 1234 ?:'";,.!&
text

# Inline Formatting
## preformat
`preformatted` `worlds`
## codeblock
### bash
```bash
curl hello "bash" &
```
### python
```python
class Hello(metaclass=Meta)
    def __init__(self):
        pass
```
### plain
```
Plain text with some keywords like:
class
def
function
#
//
/* */
```
## **Bold** *Italic* ~~Strikethrough~~
### asterisks
**bold text with ~~*double*~~ asterisks ~~‌surrounded~~** *~~really~~*
### underscores
__bold text with ~~_double_~~ underscores ~~surrounded~~__ _~~really~~_
## [Links](#Links)
This is [an example](http://example.com/) inline link.
[link to TOC](#TOC)
Footnote 1 link[^first].

Footnote 2 link[^second].

Inline footnote^[Text of inline footnote] definition.

Duplicated footnote reference[^second].

[^first]: Footnote **can have markup**

    and multiple paragraphs.

[^second]: Footnote text.

# Newlines and Trailing Spaces
P1 and

P2 with black line in between

P3 and 
P4 with whitespace at end of P3, no blank line in between

P5 and
P6 with nothing in between should be folded into one line
# Quote
> This is the first level of quoting.
>
> > This is nested blockquote.
>
> Back to the first level.

# Lists
## Ordered List
1. list item `1.`
2. list item `2.`
1. list item `1.`
2. list item `2.`

1) list item `1)`
2) list item `2)`
1) list item `1)`
2) list item `2)`
## Order List with Checkbox
1. [ ] list item `1. [ ]`
2. [x] list item `2. [x]`
1. [ ] list item `1. [ ]`
2. [x] list item `2. [x]`

1) [ ] list item `1) [ ]`
2) [x] list item `2) [x]`
1) [ ] list item `1) [ ]`
2) [x] list item `2) [x]`

## Unordered List
- list item `-`
- list item `-`
- list item `-`

* list item `*`
* list item `*`
* list item `*`

## Unordered List with Checkbox
- [ ] list item `- [ ]`
- [x] list item `- [x]`
- [ ] list item `- [ ]`
- [x] list item `- [x]`

* [ ] list item `* [ ]`
* [x] list item `* [x]`
* [ ] list item `* [ ]`
* [x] list item `* [x]`

# Table

| c1 | c2 |
|:--|:--|
| c1r1 | c2r1 |
| c1r2 | c2r2 |

# Hashtags
#markdown #editor
# TOC

{{TOC}}