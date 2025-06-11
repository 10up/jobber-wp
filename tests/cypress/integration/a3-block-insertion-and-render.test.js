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
				cy.get('select')
					.find('option[value="booking"]')
					.parent('select')
					.select('booking');
			},
		} ).then( () => {
			// cy.get('.headingTwo').should('have.text', 'New Request');

			// cy.visit( '/test-jobber-block' );
            cy.get( '.post-publish-panel__postpublish-buttons a.is-primary' ).click();

            // Check for the heading text
            cy.get('.headingTwo').should('have.text', 'New Request');
		} );
    });
});
