describe('Block Insertion', () => {
	beforeEach( () => {
		cy.login();
	} );

	it( 'User can insert the block', () => {
		// Insert Jobber block.
		cy.createPost( {
			title: 'Test Jobber Block',
			content: '',
			beforeSave: () => {
				cy.insertBlock( 'jobber/forms' );
				cy.get( 'select' )
					.find( 'option[value="booking"]' )
					.parent( 'select' )
					.select( 'booking' );
			},
		} ).then( () => {
			// Open the published post. The View Post link can open a new tab, which Cypress
			// cannot follow, so visit its URL directly.
			cy.get( '.post-publish-panel__postpublish-buttons a.is-primary' )
				.invoke( 'attr', 'href' )
				.then( ( href ) => cy.visit( href ) );

			// Check the plugin rendered the form's embed markup. The visible form itself is
			// built later by Jobber's external script, which is outside the plugin's control.
			cy.get( '.jobber-embed-block' )
				.should( 'exist' )
				.find( 'script[clienthub_id]' )
				.should( 'have.attr', 'form_url' )
				.and( 'contain', 'clienthub.getjobber.com' );
		} );
	} );
} );
