/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

const Edit = ({ attributes, setAttributes }) => {
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

		apiFetch({
			path: `jobber/v1/get_form?form_type=${formType}`,
			method: 'GET',
		})
			.then((response) => {
				const url = response?.form?.iframeUrl;
				if (!url) {
					throw new Error(__('Form URL not found in API response', 'jobber-wp'));
				}
				setIframeUrl(url);
				setLoading(false);
			})
			.catch((err) => {
				setError(err.message);
				setLoading(false);
			});
	}, [formType]);

	const blockProps = useBlockProps();
	const settingsUrl = `${window.location.origin}/wp-admin/options-general.php?page=jobber_settings`;

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Form Settings', 'jobber-wp')}>
					<SelectControl
						label={__('Form Type', 'jobber-wp')}
						value={formType}
						options={[
							{ label: __('Booking', 'jobber-wp'), value: 'booking' },
							{ label: __('Request', 'jobber-wp'), value: 'request' },
						]}
						onChange={(value) => setAttributes({ formType: value })}
					/>
				</PanelBody>
			</InspectorControls>

			{loading && <p>{__('Loading...', 'jobber-wp')}</p>}

			{error && (
				<div
					style={{
						background: '#fef2f2',
						color: '#b91c1c',
						border: '1px solid #fca5a5',
						padding: '1rem',
						borderRadius: '6px',
						marginTop: '1rem',
					}}
				>
					<p><strong>{__('Error:', 'jobber-wp')}</strong> {error}</p>
					<p>
						{__('Please make sure your Jobber account is connected.', 'jobber-wp')}{' '}
						<a href={settingsUrl} target="_blank" rel="noopener noreferrer">
							{__('Go to Jobber Settings', 'jobber-wp')}
						</a>
					</p>
				</div>
			)}

			{!loading && iframeUrl && (
				<iframe
					src={iframeUrl}
					width="100%"
					height="600"
					style={{ border: 'none' }}
					title={__('Jobber Form', 'jobber-wp')}
				/>
			)}
		</div>
	);
};

export default Edit;
