# -*- coding: utf-8 -*-
import markdown

m = markdown.Markdown(
    extensions=['markdown.extensions.sane_lists'],
    output_format='html5',
    lazy_ol=False
)