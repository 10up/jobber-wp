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
			// Save the post.
			cy.get( '.post-publish-panel__postpublish-buttons a.is-primary' ).click();

			// Check for the iframe container.
			cy.get( '.jobber-inline-work-request' ).should( 'exist' );
		} );
	} );
} );
