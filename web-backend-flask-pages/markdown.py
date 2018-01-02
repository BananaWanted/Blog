# -*- coding: utf-8 -*-
import markdown

m = markdown.Markdown(
    extensions=[
        'markdown.extensions.sane_lists',
        'markdown.extensions.extra',
        'markdown.extensions.fenced_code',
        'markdown.extensions.smart_strong',
        'markdown.extensions.tables',
        'markdown.extensions.def_list',
        'markdown.extensions.headerid',
        'markdown.extensions.nl2br',
        'markdown.extensions.toc',
    ],
    output_format='html5',
    lazy_ol=False
)