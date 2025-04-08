import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

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

		fetch(`/wp-json/jobber/v1/get_form?form_type=${formType}`)
			.then((response) => {
				console.log( 'error-response', response );
				if (!response.ok) {
					throw new Error(__('Failed to fetch form URL', 'jobber'));
				}
				return response.json();
			})
			.then((json) => {
				console.log( 'result-final', json );
				const url = json?.form?.iframeUrl;
				if (!url) {
					throw new Error(__('Form URL not found in API response', 'jobber'));
				}
				setIframeUrl(url);
				setLoading(false);
			})
			.catch((err) => {
				console.error(err.message);
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
			{error && <p style={{ color: 'red' }}>{error}</p>}

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
