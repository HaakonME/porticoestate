<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">

	<h3><xsl:value-of select="php:function('lang', 'New application')"/></h3>
	<xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

	<form action="" method="POST" id='application_form'>
		<p style="width: 750px;">For at vi skal kunne behandle din søknad
			trenger vi en del opplysninger, og vi trenger en del opplysninger
			for statistikk formål. Du må derfor fylle ut alle feltene i
			søknadskjemaet, er det vanskelig å angi eksakt så gi oss et
			estimat.</p>

		<dl class="form-2col">
			<div class="heading">1. <xsl:value-of select="php:function('lang', 'Why?')" /></div>
			<dt>
				<label for="field_activity"><xsl:value-of select="php:function('lang', 'Activity')" /></label>
				<p>Velg en aktivitet fra listsen. Finner du ikke eksakt din aktivitet, velger du hovedaktivitet, for eksempel idrett eller kultur.</p>
			</dt>
			<dd>
				<select name="activity_id" id="field_activity">
					<option value=""><xsl:value-of select="php:function('lang', '-- select an activity --')" /></option>
					<xsl:for-each select="activities">
						<option>
							<xsl:if test="../application/activity_id = id">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
							<xsl:value-of select="name"/>
						</option>
					</xsl:for-each>
				</select>
			</dd>
			<dt>
				<label for="field_description"><xsl:value-of select="php:function('lang', 'Information about the event')" /></label>
				<p><xsl:value-of select="php:function('lang', 'Short description. For public events, activities and training under the direction of organizations and clubs, this information will be displayed on the internet')" /></p>
			</dt>
			<dd>
				<textarea id="field_description" class="full-width" name="description"><xsl:value-of select="application/description"/></textarea>
			</dd>
		</dl>
		<dl class="form-col">
			<div class="heading">2. <xsl:value-of select="php:function('lang', 'How many?')" /></div>
			<p>Oppgi forventet/estimert antall deltakere. Vi vil ha det fordelt på aldersgruppeog kjønn. Dette er viktig ift. å kartlegge hvem som bruker anleggene.</p>
			<dt><label for="field_activity"><xsl:value-of select="php:function('lang', 'Estimated number of participants')" /></label></dt>
			<dd>
				<table id="agegroup">
					<tr><th/><th><xsl:value-of select="php:function('lang', 'Male')" /></th>
					    <th><xsl:value-of select="php:function('lang', 'Female')" /></th></tr>
					<xsl:for-each select="agegroups">
						<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
						<tr>
							<th><xsl:value-of select="name"/></th>
							<td>
								<input type="text">
									<xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
									<xsl:attribute name="value"><xsl:value-of select="../application/agegroups/male[../agegroup_id = $id]"/></xsl:attribute>
								</input>
							</td>
							<td>
								<input type="text">
									<xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
									<xsl:attribute name="value"><xsl:value-of select="../application/agegroups/female[../agegroup_id = $id]"/></xsl:attribute>
								</input>
							</td>
						</tr>
					</xsl:for-each>
				</table>
			</dd>
		</dl>
		<div class="clr"/>
		<dl class="form-col">
			<div class="heading">3. <xsl:value-of select="php:function('lang', 'Where?')" /></div>
			<p>Velg hus og ressurser</p>
			<dt><label for="field_building"><xsl:value-of select="php:function('lang', 'Building')" /></label></dt>
			<dd>
				<div class="autocomplete">
					<input id="field_building_id" name="building_id" type="hidden">
						<xsl:attribute name="value"><xsl:value-of select="application/building_id"/></xsl:attribute>
					</input>
					<input id="field_building_name" name="building_name" type="text">
						<xsl:attribute name="value"><xsl:value-of select="application/building_name"/></xsl:attribute>
					</input>
					<div id="building_container"/>
				</div>
			</dd>
			<dt><label for="field_resources"><xsl:value-of select="php:function('lang', 'Resources')" /></label></dt>
			<dd>
				<div id="resources_container"><xsl:value-of select="php:function('lang', 'Select a building first')" /></div>
			</dd>
		</dl>
		<dl class="form-col">
			<div class="heading">4. <xsl:value-of select="php:function('lang', 'When?')" /></div>
			<p>Velg fra dato og angi fra klokkeslett. Velg til dato og angi klokkeslett. Dato velges fra kalender. Klokkeslett angis med timer:minutter. Du kan søke om flere tider ved å velge Legg til dato.</p>
			<div id="dates-container">
				<xsl:for-each select="application/dates">
					<div class="date-container">
						<a href="#" class="close-btn">-</a>
						<dt><label for="field_from"><xsl:value-of select="php:function('lang', 'From')" /></label></dt>
						<dd class="datetime-picker">
							<input id="field_from" name="from_[]" type="text">
								<xsl:attribute name="value"><xsl:value-of select="from_"/></xsl:attribute>
							</input>
						</dd>
						<dt><label for="field_to"><xsl:value-of select="php:function('lang', 'To')" /></label></dt>
						<dd class="datetime-picker">
							<input id="field_to" name="to_[]" type="text">
								<xsl:attribute name="value"><xsl:value-of select="to_"/></xsl:attribute>
							</input>
						</dd>
					</div>
				</xsl:for-each>
			</div>


			<dt><a href="#" id="add-date-link"><xsl:value-of select="php:function('lang', 'Add another date')" /></a></dt>
		</dl>
		<dl class="form-col">
			<div class="heading">5. <xsl:value-of select="php:function('lang', 'Who?')" /></div>
			<p>Kryss av for hvilken målgruppe arrangementet/treningen er rettet mot. Du kan krysse av for flere.</p>
			<dt><label for="field_from"><xsl:value-of select="php:function('lang', 'Target audience')" /></label></dt>
			<dd>
				<div id="audience_container">&nbsp;</div>
			</dd>
		</dl>
		<div class="clr"/>
		<dl class="form-col">
			<div class="heading"><br />6. <xsl:value-of select="php:function('lang', 'Contact information')" /></div>
			<p>Oppgi ditt navn (fornavn og etternavn), e-post adresse vi kan nå deg på og telefon, mobil om du vil ha melding på SMS.</p>
			<dt><label for="field_contact_name"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
			<dd>
				<input id="field_contact_name" name="contact_name" type="text">
					<xsl:attribute name="value"><xsl:value-of select="application/contact_name"/></xsl:attribute>
				</input>
			</dd>
			<dt><label for="field_contact_email"><xsl:value-of select="php:function('lang', 'Email')" /></label></dt>
			<dd>
				<input id="field_contact_email" name="contact_email" type="text">
					<xsl:attribute name="value"><xsl:value-of select="application/contact_email"/></xsl:attribute>
				</input>
			</dd>
			<dt><label for="field_contact_phone"><xsl:value-of select="php:function('lang', 'Phone')" /></label></dt>
			<dd>
				<input id="field_contact_phone" name="contact_phone" type="text">
					<xsl:attribute name="value"><xsl:value-of select="application/contact_phone"/></xsl:attribute>
				</input>
			</dd>
		</dl>
		<dl class="form-col">
			<div class="heading">7. <xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" /></div>
			<p>Ut fra reglementet i pkt. 8 finner du kriterier for fakturering. Når du som privatperson skal låne noe som det ikke skal faktureres for oppgir du kun fødselsdato, men skal du leie noe som koster noe, da må vi ha hele personnummeret. Alle lag og organisasjoner skal oppgi organisasjonsnr.</p>
			<xsl:copy-of select="phpgw:booking_customer_identifier(application, '')"/>
			<br />
			<p><xsl:value-of select="php:function('lang', 'In order to send the invoice we need information about either customer organization number or norwegian social security number')" /></p>
		</dl>
		<dl class="form-col">
			<div class="heading"><br />8. <xsl:value-of select="php:function('lang', 'Terms and conditions')" /></div>
			<p>Alle som leier lokaler hos Bergen kommune må bekrefte at de har lest betingelsene, dette gjelder som regel brannforskrifter og husreglement.</p>
			<br />
			<div id='regulation_documents'>&nbsp;</div>
			<br />
			<p><xsl:value-of select="php:function('lang', 'To borrow premises you must verify that you have read terms and conditions')" /></p>
		</dl>
		<div class="form-buttons">
			<input type="submit">
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Send')"/></xsl:attribute>
			</input>
			<a class="cancel">
				<xsl:attribute name="href"><xsl:value-of select="application/cancel_link"/></xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
			<p style="width: 750px; margin-top: 10px;">Trykker du <strong>SEND</strong>-knappen får du opp en rød melding øverst om noen opplysninger mangler, er alt OK kommer det opp en grønn melding. Det blir sendt en bekreftelse til din e-post, og en lenke hvor du kan gå inn og se status og legge til ekstra opplysninger i saken.<br /><br />
				Trykker du <strong>Avbryt</strong> blir søknaden ikke sendt eller lagret, og du går tilbake til kalenderen.</p>
		</div>
	</form>
	</div>
	<script type="text/javascript">
		YAHOO.booking.initialDocumentSelection = <xsl:value-of select="application/accepted_documents_json"/>;
		YAHOO.booking.initialAcceptAllTerms = false;
		YAHOO.booking.initialSelection = <xsl:value-of select="application/resources_json"/>;
		YAHOO.booking.initialAudience = <xsl:value-of select="application/audience_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'From', 'To', 'Resource Type', 'Name', 'Accepted', 'Document', 'You must accept to follow all terms and conditions of lease first.')"/>;
	</script>
</xsl:template>
