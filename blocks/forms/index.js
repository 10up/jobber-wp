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

	/**
	 * Legacy request blocks were saved without attributes, because "request" was the
	 * formType default. Inserting new blocks with an empty formType is what tells the
	 * two apart, so only new blocks preselect the account's default form.
	 */
	variations: [
		{
			name: 'jobber-form',
			title: metadata.title,
			description: metadata.description,
			icon: BlockIcon,
			attributes: { formType: '' },
			isDefault: true,
			scope: ['inserter'],
		},
	],
});
