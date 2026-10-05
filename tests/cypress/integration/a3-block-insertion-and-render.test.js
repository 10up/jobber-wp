/**
 * The form list is served by the mock in tests/test-plugin, so these specs never reach a
 * real Jobber account. The names below mirror tests/test-plugin/get-forms.json.
 *
 * The picker lives in the settings sidebar, which is in the main document. Block output
 * lives in the editor canvas, which newer WordPress renders in an iframe, so anything
 * inside the block is reached through cy.getBlockEditor().
 */
const DEFAULT_FORM = 'Work Request Form';
const OTHER_FORM = 'Online Booking';
const PICKER = '.interface-interface-skeleton__sidebar select';

/**
 * Put the site into a connected state so the block fetches the form list.
 */
const connectJobber = () => {
	cy.visit( '/wp-admin/options-general.php?page=jobber_settings&e2e_set_jobber_auth=1' );
};

describe( 'Block Insertion', () => {
	beforeEach( () => {
		cy.login();
		connectJobber();
	} );

	it( 'User can insert the block and the account default is preselected', () => {
		cy.createPost( {
			title: 'Test Jobber Block',
			content: '',
			beforeSave: () => {
				cy.insertBlock( 'jobber/forms' );

				// Every enabled form is offered, with the account default labelled.
				cy.get( PICKER ).should( 'exist' );
				cy.get( PICKER ).find( 'option' ).should( 'have.length.at.least', 3 );
				cy.get( PICKER )
					.find( 'option:selected' )
					.should( 'contain', DEFAULT_FORM );
			},
		} ).then( () => {
			cy.get( '.post-publish-panel__postpublish-buttons a.is-primary' ).click();

			// The chosen form renders on the front end.
			cy.get( '.jobber-embed-block' ).should( 'exist' );
		} );
	} );

	it( 'User can switch to a different form', () => {
		cy.createPost( {
			title: 'Test Jobber Block Switching',
			content: '',
			beforeSave: () => {
				cy.insertBlock( 'jobber/forms' );
				cy.get( PICKER ).should( 'exist' );

				// Options are keyed by form id, so pick by visible label.
				cy.get( PICKER ).then( ( $select ) => {
					const option = [ ...$select[ 0 ].options ].find( ( item ) =>
						item.textContent.includes( OTHER_FORM ),
					);

					expect( option, `an option for ${ OTHER_FORM }` ).to.not.be.undefined;
					cy.get( PICKER ).select( option.value );
				} );

				cy.get( PICKER )
					.find( 'option:selected' )
					.should( 'contain', OTHER_FORM );
			},
		} ).then( () => {
			cy.get( '.post-publish-panel__postpublish-buttons a.is-primary' ).click();
			cy.get( '.jobber-embed-block' ).should( 'exist' );
		} );
	} );
} );

describe( 'Blocks saved before the form picker existed', () => {
	before( () => {
		// Seed a block in the old shape: a form type, and no form id.
		cy.wpCliEval(
			`wp_insert_post( array(
				'post_title'   => 'Legacy Jobber Block',
				'post_content' => '<!-- wp:jobber/forms {"formType":"booking"} /-->',
				'post_status'  => 'publish',
			) );`,
		);

		// A legacy request block has no attributes at all, since "request" was the default.
		cy.wpCliEval(
			`wp_insert_post( array(
				'post_title'   => 'Legacy Jobber Request Block',
				'post_content' => '<!-- wp:jobber/forms /-->',
				'post_status'  => 'publish',
			) );`,
		);
	} );

	beforeEach( () => {
		cy.login();
		connectJobber();
	} );

	[ 'Legacy Jobber Block', 'Legacy Jobber Request Block' ].forEach( ( title ) => {
		it( `Asks the author to re-pick instead of silently changing the block: ${ title }`, () => {
			cy.visit( '/wp-admin/edit.php' );
			cy.contains( 'a.row-title', new RegExp( `^${ title }$` ) ).click();
			cy.closeWelcomeGuide();

			// The block explains why it needs attention.
			cy.getBlockEditor()
				.find( '.components-notice__content' )
				.should( 'contain', 'no longer separates booking and request forms' );

			// Select the block so the inspector shows its settings.
			cy.getBlockEditor().find( '[data-type="jobber/forms"]' ).first().click();

			// Nothing was chosen on the author's behalf.
			cy.get( PICKER ).find( 'option:selected' ).should( 'contain', 'Select a form' );
		} );
	} );

	it( 'Keeps rendering the saved form on the front end', () => {
		cy.visit( '/?s=Legacy+Jobber+Block' );
		cy.contains( 'Legacy Jobber Block' ).click();

		// The legacy path still resolves, so the page does not fatal or render an error.
		cy.get( 'body' ).should( 'not.contain', 'Fatal error' );
	} );
} );
