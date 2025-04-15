/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, Placeholder, SelectControl, Spinner } from '@wordpress/components';
import { calendar } from '@wordpress/icons';
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

			{loading && <Spinner />}

			{error && (
				<Placeholder icon={calendar} label={__('Jobber Forms', 'jobber-wp')} isColumnLayout>
					<p style={{ marginBottom: '0' }}>
						{__('The following error was encountered:', 'jobber-wp')}{' '}
						<span style={{ color: '#b91c1c' }}>
							<strong>{__('Error:', 'jobber-wp')}</strong> {error}
						</span>
					</p>
					<p style={{ marginTop: '0', marginBottom: '0' }}>
						{__(
							'Double check the Jobber settings to ensure your account is properly connected.',
							'jobber-wp',
						)}{' '}
					</p>
					<Button
						variant="secondary"
						onClick={() => window.open(settingsUrl, '_blank')}
						style={{ width: 'fit-content' }}
					>
						{__('Go to Jobber Settings', 'jobber-wp')}
					</Button>
				</Placeholder>
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
