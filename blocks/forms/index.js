/**
 * WordPress dependencies.
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Edit from './edit';
import Save from './save';
import metadata from './block.json';
import { BlockIcon } from './icon';

/**
 * Register new block type.
 */
registerBlockType(metadata.name, {
	/**
	 * Block icon.
	 *
	 * @see ./icon.js
	 */
	icon: BlockIcon,

	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	Save,
});
