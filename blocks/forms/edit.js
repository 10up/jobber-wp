/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	Button,
	Disabled,
	Notice,
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

/**
 * Get the preview height for a form.
 *
 * Jobber's BookingType enum is NONE, JOB or ASSESSMENT. NONE creates a request only, so it
 * renders the long work request form. JOB and ASSESSMENT both create a booking and show the
 * shorter scheduler. An unrecognised value gets the taller height rather than risking a cut
 * off form. Mirrors Blocks::get_form_height() on the PHP side.
 *
 * @param {string} bookingType The form's bookingType value.
 * @returns {number} Height in pixels.
 */
const BOOKABLE_TYPES = ['JOB', 'ASSESSMENT'];

const getFormHeight = (bookingType) =>
	BOOKABLE_TYPES.includes(String(bookingType).toUpperCase()) ? 400 : 1630;

const Edit = ({ attributes, setAttributes }) => {
	const { formId, formName, bookingType, formType } = attributes;
	const [forms, setForms] = useState([]);
	const [loading, setLoading] = useState(true);
	const [error, setError] = useState(null);

	useEffect(() => {
		let cancelled = false;

		setLoading(true);
		setError(null);

		apiFetch({ path: 'jobber/v1/get_forms', method: 'GET' })
			.then((response) => {
				if (cancelled) {
					return;
				}

				const list = response?.forms ?? [];
				setForms(list);
				setLoading(false);

				// Only a genuinely new block auto-selects. A legacy block carries a
				// formType and no formId, and must keep rendering what it saved until
				// an author re-picks, so it is left untouched here.
				if (!formId && !formType && list.length) {
					const preferred = list.find((form) => form.isDefault) ?? list[0];
					setAttributes({
						formId: preferred.id,
						formName: preferred.name,
						bookingType: preferred.bookingType,
					});
				}
			})
			.catch((err) => {
				if (cancelled) {
					return;
				}
				setError(err.message);
				setLoading(false);
			});

		return () => {
			cancelled = true;
		};
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, []);

	const blockProps = useBlockProps();
	const settingsUrl = `${window.location.origin}/wp-admin/options-general.php?page=jobber_settings`;

	const selected = forms.find((form) => form.id === formId);
	const previewUrl = selected?.url ?? '';
	// A legacy block saved a form type rather than a specific form, so it needs re-picking.
	const needsMigration = !formId && !!formType;

	const onSelectForm = (value) => {
		const form = forms.find((item) => item.id === value);

		setAttributes({
			formId: value,
			formName: form?.name ?? '',
			bookingType: form?.bookingType ?? '',
		});
	};

	if (loading) {
		return (
			<div {...blockProps}>
				<Placeholder icon={BlockIcon} label={__('Jobber', 'jobber')}>
					<Spinner />
				</Placeholder>
			</div>
		);
	}

	if (error) {
		return (
			<div {...blockProps}>
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
						)}
					</p>
					<Button
						variant="secondary"
						onClick={() => window.open(settingsUrl, '_blank')}
						style={{ width: 'fit-content' }}
					>
						{__('Go to Jobber Settings', 'jobber')}
					</Button>
				</Placeholder>
			</div>
		);
	}

	if (!forms.length) {
		return (
			<div {...blockProps}>
				<Placeholder icon={BlockIcon} label={__('Jobber', 'jobber')} isColumnLayout>
					<p style={{ marginBottom: '0' }}>
						{__(
							'No enabled forms were found on your Jobber account. Create or enable a form in Jobber, then reload this page.',
							'jobber',
						)}
					</p>
				</Placeholder>
			</div>
		);
	}

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Form Settings', 'jobber')}>
					<SelectControl
						label={__('Form', 'jobber')}
						value={selected ? formId : ''}
						options={[
							// Also shown when the saved form no longer exists, so the dropdown never
							// appears to have a form selected that it does not.
							...(selected
								? []
								: [{ label: __('Select a form', 'jobber'), value: '' }]),
							...forms.map((form) => ({
								label: form.isDefault
									? sprintf(
											/* translators: %s: form name. */
											__('%s (default)', 'jobber'),
											form.name,
										)
									: form.name,
								value: form.id,
							})),
						]}
						onChange={onSelectForm}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			{needsMigration && (
				<Notice status="warning" isDismissible={false}>
					{__(
						'Jobber no longer separates booking and request forms. Choose which form this block should display.',
						'jobber',
					)}
				</Notice>
			)}

			{!previewUrl && !needsMigration && (
				<Notice status="warning" isDismissible={false}>
					{__('The selected form is no longer available. Choose another form.', 'jobber')}
				</Notice>
			)}

			{previewUrl && (
				<Disabled>
					<iframe
						src={previewUrl}
						style={{
							border: '1px dashed #E0E0E0',
							height: `${getFormHeight(bookingType)}px`,
							width: '100%',
						}}
						title={formName || __('Jobber Form', 'jobber')}
					/>
				</Disabled>
			)}
		</div>
	);
};

export default Edit;
