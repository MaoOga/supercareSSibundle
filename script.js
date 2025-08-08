        function autoGrow(textarea) {
            textarea.style.height = "auto";
            textarea.style.height = (textarea.scrollHeight) + "px";
        }

        $(function() {
            $('.datepicker').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd/mm/yy',
                showButtonPanel: true
            });
            // Disable browser autofill suggestions on date inputs
            $('.datepicker').attr({
                autocomplete: 'off',
                autocorrect: 'off',
                autocapitalize: 'off',
                spellcheck: 'false'
            });
        });

        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        function startDrawing(e) {
            isDrawing = true;
            [lastX, lastY] = getMousePos(e);
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const [x, y] = getMousePos(e);
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(x, y);
            ctx.strokeStyle = '#000';
            ctx.lineWidth = 2;
            ctx.stroke();
            [lastX, lastY] = [x, y];
        }

        function stopDrawing() {
            isDrawing = false;
        }

        function getMousePos(e) {
            const rect = canvas.getBoundingClientRect();
            let x, y;
            if (e.type.startsWith('touch')) {
                x = e.touches[0].clientX - rect.left;
                y = e.touches[0].clientY - rect.top;
            } else {
                x = e.clientX - rect.left;
                y = e.clientY - rect.top;
            }
            return [x, y];
        }

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);
        canvas.addEventListener('touchstart', startDrawing);
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', stopDrawing);

        function printForm() {
            window.print();
        }

        // Hide empty datepicker inputs on print, restore after
        (function setupPrintVisibilityForEmptyDatepickers() {
            function toggleEmptyDatepickers(hide) {
                $('.datepicker').each(function() {
                    const isEmpty = !$(this).val();
                    if (isEmpty) {
                        if (hide) {
                            $(this).addClass('hide-on-print');
                        } else {
                            $(this).removeClass('hide-on-print');
                        }
                    }
                });
            }

            // Before print
            if (window.matchMedia) {
                const mediaQueryList = window.matchMedia('print');
                mediaQueryList.addListener(function(mql) {
                    if (mql.matches) {
                        toggleEmptyDatepickers(true);
                    } else {
                        toggleEmptyDatepickers(false);
                    }
                });
            }

            window.addEventListener('beforeprint', function() {
                toggleEmptyDatepickers(true);
            });
            window.addEventListener('afterprint', function() {
                toggleEmptyDatepickers(false);
            });
        })();

        function showMessage(message, type) {
            const messageBox = $('#messageBox');
            messageBox.text(message)
                .removeClass('success error')
                .addClass(type)
                .show();
            setTimeout(function() {
                messageBox.hide();
            }, 4000);
        }

        function addAntibioticRow() {
            const table = $('#antibiotic-table tbody');
            const rowCount = table.find('.antibiotic-row').length + 1;
            const newRow = `
                <tr class="antibiotic-row">
                    <td class="tg-k2l0">${rowCount}</td>
                    <td class="tg-1wig"><textarea name="drug-name_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2;"></textarea></td>
                    <td class="tg-1wig"><textarea name="dosage_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2;"></textarea></td>
                    <td class="tg-1wig"><input type="text" name="antibiotic_usage[startedon]_${rowCount}" class="datepicker" placeholder="dd/mm/yyyy" style="width: 100px;" value=""></td>
                    <td class="tg-1wig"><input type="text" name="antibiotic_usage[stoppeon]_${rowCount}" class="datepicker" placeholder="dd/mm/yyyy" style="width: 100px;" value=""></td>
                </tr>
            `;
            table.append(newRow);
            table.find('.datepicker').datepicker({
                dateFormat: "dd/mm/yy",
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true
            }).attr({
                autocomplete: 'off',
                autocorrect: 'off',
                autocapitalize: 'off',
                spellcheck: 'false'
            });
            updateAntibioticRowNumbers();
        }

        function removeAntibioticRow() {
            const table = $('#antibiotic-table tbody');
            const rows = table.find('.antibiotic-row');
            if (rows.length > 1) {
                rows.last().remove();
                updateAntibioticRowNumbers();
            } else {
                showMessage('At least one row must remain.', 'error');
            }
        }

        function updateAntibioticRowNumbers() {
            const rows = $('#antibiotic-table tbody .antibiotic-row');
            rows.each(function(index) {
                $(this).find('td:first-child').text(index + 1);
                $(this).find('textarea, input').each(function() {
                    const name = $(this).attr('name');
                    const newName = name.replace(/_\d+$/, `_${index + 1}`);
                    $(this).attr('name', newName);
                });
            });
        }

        function addDrainRow() {
            const table = $('table.tg:has(.drain-row) tbody');
            const rowCount = table.find('.drain-row').length + 1;
            const newRow = `
                <tr class="drain-row">
                    <td class="tg-1wig" colspan="4">
                        <label class="flex gap-2 items-center w-full">
                            <span class="whitespace-nowrap label-bold">${rowCount}. </span>
                            <textarea name="drain_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2;"></textarea>
                        </label>
                    </td>
                </tr>
            `;
            table.append(newRow);
            table.find('textarea').off('input').on('input', function() { autoGrow(this); });
        }

        function removeDrainRow() {
            const table = $('table.tg:has(.drain-row) tbody');
            const rows = table.find('.drain-row');
            if (rows.length > 1) {
                rows.last().remove();
                updateDrainRowNumbers();
            } else {
                showMessage('At least one row must remain.', 'error');
            }
        }

        function updateDrainRowNumbers() {
            const rows = $('table.tg:has(.drain-row) tbody .drain-row');
            rows.each(function(index) {
                $(this).find('span').text(`${index + 1}.`);
                $(this).find('textarea').attr('name', `drain_${index + 1}`);
            });
        }

        function addPostOperativeRow() {
            const table = $('#post-operative-table tbody');
            const rowCount = table.find('.post-operative-row').length + 1;
            const newRow = `
                <tr class="post-operative-row">
                    <td class="tg-k2l0">${rowCount}</td>
                    <td class="tg-0lax"><input type="text" name="post-operative[date]_${rowCount}" class="datepicker" placeholder="dd/mm/yyyy" style="width: 100px;" value=""></td>
                    <td class="tg-0lax"><textarea name="post-dosage_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2; text-align:center;"></textarea></td>
                    <td class="tg-0lax"><textarea name="type-ofdischarge_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2; text-align:center;"></textarea></td>
                    <td class="tg-0lax"><textarea name="tenderness-pain_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2; text-align:center;"></textarea></td>
                    <td class="tg-0lax"><textarea name="swelling_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2; text-align:center;"></textarea></td>
                    <td class="tg-0lax"><textarea name="Fever_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2; text-align:center;"></textarea></td>
                </tr>
            `;
            table.append(newRow);
            table.find('.datepicker').datepicker({
                dateFormat: "dd/mm/yy",
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true
            }).attr({
                autocomplete: 'off',
                autocorrect: 'off',
                autocapitalize: 'off',
                spellcheck: 'false'
            });
            table.find('textarea').off('input').on('input', function() { autoGrow(this); });
            updatePostOperativeRowNumbers();
        }

        function removePostOperativeRow() {
            const table = $('#post-operative-table tbody');
            const rows = table.find('.post-operative-row');
            if (rows.length > 1) {
                rows.last().remove();
                updatePostOperativeRowNumbers();
            } else {
                showMessage('At least one row must remain.', 'error');
            }
        }

        function updatePostOperativeRowNumbers() {
            const rows = $('#post-operative-table tbody .post-operative-row');
            rows.each(function(index) {
                $(this).find('td:first-child').text(index + 1);
                $(this).find('textarea, input').each(function() {
                    const name = $(this).attr('name');
                    const newName = name.replace(/_\d+$/, `_${index + 1}`);
                    $(this).attr('name', newName);
                });
            });
        }