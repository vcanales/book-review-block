/**
 * External dependencies
 */
import classnames from 'classnames';

const { registerBlockType } = wp.blocks;

/**
 * Internal dependencies
 */
import './editor.scss';
import './style.scss';
import edit from './edit';
import { BookReviewBlockIcon } from './icon';

const ratings = [
	{ rating: 5, title: 'it was amazing' },
	{ rating: 4, title: 'really liked it' },
	{ rating: 3, title: 'liked it' },
	{ rating: 2, title: 'it was ok' },
	{ rating: 1, title: 'did not like it' },
];

registerBlockType( 'book-review-block/book-review', {
	title: 'Review',
	description: 'Add book details such as title, author, publisher and cover image to enhance your review posts.',
	icon: BookReviewBlockIcon,
	category: 'book-review',
	supports: {
		anchor: true,
		multiple: false,
	},
	attributes: {
		alt: {
			type: 'string',
			source: 'attribute',
			selector: 'img',
			attribute: 'alt',
		},
		backgroundColor: {
			type: 'string',
		},
		id: {
			type: 'number',
		},
		book_review_author: {
			type: 'string',
			source: 'meta',
			meta: 'book_review_author',
		},
		book_review_cover_url: {
			type: 'string',
			source: 'meta',
			meta: 'book_review_cover_url',
		},
		book_review_format: {
			type: 'string',
			source: 'meta',
			meta: 'book_review_format',
		},
		book_review_genre: {
			type: 'string',
			source: 'meta',
			meta: 'book_review_genre',
		},
		book_review_pages: {
			type: 'string',
			source: 'meta',
			meta: 'book_review_pages',
		},
		book_review_publisher: {
			type: 'string',
			source: 'meta',
			meta: 'book_review_publisher',
		},
		book_review_rating: {
			type: 'string',
			source: 'meta',
			meta: 'book_review_rating',
		},
		book_review_release_date: {
			type: 'string',
			source: 'meta',
			meta: 'book_review_release_date',
		},
		book_review_series: {
			type: 'string',
			source: 'meta',
			meta: 'book_review_series',
		},
		book_review_source: {
			type: 'string',
			source: 'meta',
			meta: 'book_review_source',
		},
		book_review_summary: {
			type: 'string',
			source: 'meta',
			meta: 'book_review_summary',
		},
		book_review_title: {
			type: 'string',
			source: 'meta',
			meta: 'book_review_title',
		},
	},
	edit,
	save: () => {
		return null;
	},
	deprecated: [ {
		attributes: {
			alt: {
				type: 'string',
				source: 'attribute',
				selector: 'img',
				attribute: 'alt',
			},
			author: {
				type: 'array',
				source: 'children',
				selector: '.book-review-block__author',
			},
			backgroundColor: {
				type: 'string',
			},
			format: {
				type: 'array',
				source: 'children',
				selector: '.book-review-block__format',
			},
			genre: {
				type: 'array',
				source: 'children',
				selector: '.book-review-block__genre',
			},
			pages: {
				type: 'array',
				source: 'children',
				selector: '.book-review-block__pages',
			},
			publisher: {
				type: 'array',
				source: 'children',
				selector: '.book-review-block__publisher',
			},
			rating: {
				type: 'string',
			},
			releaseDate: {
				type: 'array',
				source: 'children',
				selector: '.book-review-block__release-date',
			},
			series: {
				type: 'array',
				source: 'children',
				selector: '.book-review-block__series',
			},
			source: {
				type: 'array',
				source: 'children',
				selector: '.book-review-block__source',
			},
			summary: {
				type: 'array',
				source: 'children',
				selector: '.book-review-block__description',
			},
			title: {
				type: 'array',
				source: 'children',
				selector: '.book-review-block__title',
			},
			url: {
				type: 'string',
				source: 'attribute',
				selector: 'img',
				attribute: 'src',
			},
		},

		save( { attributes } ) {
			const {
				alt,
				author = [],
				backgroundColor,
				format = [],
				genre = [],
				pages = [],
				publisher = [],
				rating: reviewRating,
				releaseDate = [],
				series = [],
				source = [],
				summary = [],
				title = [],
				url,
			} = attributes;
			const className = classnames( 'book-review-block', {
				'has-background': backgroundColor,
			} );
			const styles = {
				backgroundColor: backgroundColor,
			};

			return (
				<div
					className={ className }
					style={ styles }>
					{ !! url && (
						<div className="book-review-block__cover-wrapper">
							<img className="book-review-block__cover" src={ url } alt={ alt } />
						</div>
					) }
					<div className="book-review-block__details">
						{ title.length > 0 && <span className="book-review-block__title">{ title }</span> }
						{ series.length > 0 && <span className="book-review-block__series">{ series }</span> }
						{ author.length > 0 && <span className="book-review-block__author">{ author }</span> }
						{ genre.length > 0 && <span className="book-review-block__genre">{ genre }</span> }
						{ publisher.length > 0 && <span className="book-review-block__publisher">{ publisher }</span> }
						{ releaseDate.length > 0 && <span className="book-review-block__release-date">{ releaseDate }</span> }
						{ format.length > 0 && <span className="book-review-block__format">{ format }</span> }
						{ pages.length > 0 && <span className="book-review-block__pages">{ pages }</span> }
						{ source.length > 0 && <span className="book-review-block__source">{ source }</span> }

						<div className="book-review-block__rating">
							{ ratings.map( ( { rating } ) => (
								<span
									className={ reviewRating && rating <= reviewRating ? 'on' : 'off' }
									data-rating={ rating }
									key={ rating }>
									&#9734;
								</span>
							) ) }
						</div>

						{ summary.length > 0 && <div className="book-review-block__description">{ summary }</div> }
					</div>
				</div>
			);
		},
	} ],
} );
