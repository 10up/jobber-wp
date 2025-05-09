/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	Button,
	Disabled,
	PanelBody,
	Placeholder,
	SelectControl,
	Spinner,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { BlockIcon } from './icon';

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
			path: `jobber/v1/get_form?form_type=${formType}&force=true`,
			method: 'GET',
		})
			.then((response) => {
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

	const blockProps = useBlockProps();
	const settingsUrl = `${window.location.origin}/wp-admin/options-general.php?page=jobber_settings`;

	return (
		<div {...blockProps}>
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

			{loading && <Spinner />}

			{error && (
				<Placeholder icon={BlockIcon} label={__('Jobber', 'jobber')} isColumnLayout>
					<p style={{ marginBottom: '0' }}>
						{__('The following error was encountered:', 'jobber')}{' '}
						<span style={{ color: '#b91c1c' }}>
							<strong>{__('Error:', 'jobber')}</strong> {error}
						</span>
					</p>
					<p style={{ marginTop: '0', marginBottom: '0' }}>
						{__(
							'Double check the Jobber settings to ensure your account is properly connected.',
							'jobber',
						)}{' '}
					</p>
					<Button
						variant="secondary"
						onClick={() => window.open(settingsUrl, '_blank')}
						style={{ width: 'fit-content' }}
					>
						{__('Go to Jobber Settings', 'jobber')}
					</Button>
				</Placeholder>
			)}

			{!loading && iframeUrl && (
				<Disabled>
					<iframe
						src={iframeUrl}
						style={{
							border: '1px dashed #E0E0E0',
							height: '500px',
							width: '100%',
						}}
						title={__('Jobber Form', 'jobber')}
					/>
				</Disabled>
			)}
		</div>
	);
};

export default Edit;
