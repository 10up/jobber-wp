import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { TextareaControl, PanelBody } from '@wordpress/components';
import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { embedCode } = attributes;

	return (
		<div {...useBlockProps()}>
			<h3>{__('Jobber Data here:', 'jobber')}</h3>

			<InspectorControls>
				<PanelBody title={__('Embed Code', 'jobber')}>
					<TextareaControl
						label={__('Paste Jobber Embed Code', 'jobber')}
						value={embedCode}
						onChange={(value) => setAttributes({ embedCode: value })}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...useBlockProps()}>
				{embedCode ? (
					<div dangerouslySetInnerHTML={{ __html: embedCode }} />
				) : (
					<p>{__('Paste your Jobber embed code in the sidebar.', 'jobber')}</p>
				)}
			</div>

			{loading && <p>{__('Loading...', 'jobber')}</p>}
			{error && <p style={{ color: 'red' }}>{error}</p>}
			{data && <pre>{JSON.stringify(data, null, 2)}</pre>}
		</div>
	);
}
