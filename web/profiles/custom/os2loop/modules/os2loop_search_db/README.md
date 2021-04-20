# OS2Loop Search DB

Two exposed forms are available as blocks: One with just the search field and
one with the search field plus sorting of results.

The former is shown on all node types that are indexed and the latter is shown
only on the search page itself.

## Comments in search

Comments, i.e. answers on questions and comments on posts, are included in the
search index and a facet for filtering on comment type is set up. However, we
don't expose this facet to the user. Instead we use it to include comments on
content when filtering on content type, i.e. when filtering on “Question” we
also include “Answer” (a comment type) in the search query (see
`Drupal\os2loop_search_db\Helper\Helper::alterSearchApiQuery` for implementation
details).
