{**
 * lib/pkp/templates/controllers/modals/submission/viewSubmissionMetadata.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Display submission metadata.
 *}

<div id="viewSubmissionMetadata" class="">
	<h3>{$publication->getLocalizedFullTitle()|strip_unsafe_html}</h3>
	{if $authors}<h4>{$authors|escape}</h4>{/if}
	<div class="abstract">
		{$publication->getLocalizedData('abstract')|strip_unsafe_html}
	</div>
	{if $additionalMetadata || $dataAvailability}
		<table class="pkpTable">
		{foreach $additionalMetadata as $metadata}
			<tr>
				{foreach $metadata as $metadataItem}
					{if $metadataItem@iteration % 2 != 0}
						<th scope="row">{$metadataItem|escape}</th>
					{else}
						<td>{$metadataItem|escape}</td>
					{/if}
				{/foreach}
			</tr>
		{/foreach}
		{if $dataAvailability}
			<tr>
				<th scope="row">
					{translate key="submission.dataAvailability"}
				</th>
				<td>
					{$dataAvailability|strip_unsafe_html}
				</td>
			</tr>
		{/if}
		</table>
	{/if}
</div>
