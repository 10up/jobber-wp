/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

export default function Edit({ attributes, setAttributes }) {
	const { formType } = attributes;
	const [iframeUrl, setIframeUrl] = useState('');
	const [loading, setLoading] = useState(false);
	const [error, setError] = useState(null);

	useEffect(() => {
		if (!formType) {
			setIframeUrl('');
			return;
		}

		setLoading(true);
		setError(null);

		apiFetch( {
			path: `jobber/v1/get_form?form_type=${formType}`,
			method: 'GET',
		} ).then((response) => {
				const url = response?.form?.iframeUrl;
				if (!url) {
					throw new Error(__('Form URL not found in API response', 'jobber'));
				}
				setIframeUrl(url);
				setLoading(false);
			})
			.catch((err) => {
				setError(err.message);
				setLoading(false);
			});
	}, [formType]);

	return (
		<div {...useBlockProps()}>
			{loading && <h3>{__('Jobber Form', 'jobber')}</h3>}

			<InspectorControls>
				<PanelBody title={__('Form Settings', 'jobber')}>
					<SelectControl
						label={__('Form Type', 'jobber')}
						value={formType}
						options={[
							{ label: __('Booking', 'jobber'), value: 'booking' },
							{ label: __('Request', 'jobber'), value: 'request' },
						]}
						onChange={(value) => setAttributes({ formType: value })}
					/>
				</PanelBody>
			</InspectorControls>

			{loading && <p>{__('Loading...', 'jobber')}</p>}
			{error && <p style={{ color: '#cc1818', border: '1px solid #e0e0e0', padding: '1rem' }}>{__('Error:', 'jobber')} {error}</p>}

			{!loading && iframeUrl && (
				<iframe
					src={iframeUrl}
					width="100%"
					height="600"
					style={{ border: 'none' }}
					title={__('Jobber Form', 'jobber')}
				/>
			)}
		</div>
	);
}
