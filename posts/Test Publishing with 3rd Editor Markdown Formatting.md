# Test Publishing with 3rd Editor Markdown Formatting
This post is for testing the `publishing` feature provided by some 3rd party editors.

The original markdown file can be viewed [here][1]

Editors tested in this post:
- [iA Writer][2]
- [Byword][3]
- [Ulysses][4]

This post is published with latest macOS editions as of the commit date on GitHub.

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
### whitespace indented
```
Plain text with some keywords like:
class
def
function
#
//
/* */
```
## **Bold** *Italic* \~\~Strikethrough\~\\~
### asterisks
**bold text with \~\~*double*\~\~ asterisks \~\~‌surrounded\~\~** *\~\~really\~\~*
### underscores
\__bold text with \~\~_double_\~\~ underscores \~\~surrounded\~\~\_\_ _\~\~really\~\~_ ## [Links][5]
[External link to example site][6] and
[internal link to TOC][7]

Footnote 1 link[^1].

Footnote 2 link[^2].

Duplicated footnote reference[^3].

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
# markdown #editor
# TOC

{{TOC}}

[^1]:	Footnote **can have markup**

	and multiple paragraphs.

[^2]:	Footnote text.

[^3]:	Footnote text.

[1]:	https://raw.githubusercontent.com/BananaWanted/Blog/master/posts/Test%20Publishing%20with%203rd%20Editor%20Markdown%20Formatting.md
[2]:	https://ia.net/writer
[3]:	https://bywordapp.com/
[4]:	https://ulysses.app/
[5]:	#Links
[6]:	http://example.com/
[7]:	#TOC