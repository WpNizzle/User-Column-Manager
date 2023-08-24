document.addEventListener('DOMContentLoaded', function () {
    const customColumnsList = document.getElementById('custom-columns-list');
    if (customColumnsList) {
        const columns = customColumnsList.getElementsByClassName('custom-column-item');
        const deleteColumnButtons = customColumnsList.getElementsByClassName('delete-column');

        for (let i = 0; i < columns.length; i++) {
            columns[i].draggable = true;

            columns[i].addEventListener('dragstart', function (event) {
                event.dataTransfer.setData('text/plain', event.target.dataset.columnKey);
            });
        }

        customColumnsList.addEventListener('dragover', function (event) {
            event.preventDefault();
        });

        customColumnsList.addEventListener('drop', function (event) {
            event.preventDefault();
            const columnKey = event.dataTransfer.getData('text/plain');
            const targetColumn = event.target.closest('.custom-column-item');

            if (targetColumn) {
                const targetKey = targetColumn.dataset.columnKey;
                const targetIndex = Array.from(columns).indexOf(targetColumn);

                if (targetKey !== columnKey) {
                    const columnToMove = customColumnsList.querySelector(`[data-column-key="${columnKey}"]`);
                    const columnIndex = Array.from(columns).indexOf(columnToMove);

                    customColumnsList.insertBefore(columnToMove, targetIndex > columnIndex ? targetColumn.nextSibling : targetColumn);

                    // Update the order of columns in the backend
                    const newOrder = Array.from(customColumnsList.getElementsByClassName('custom-column-item'))
                        .map(item => item.dataset.columnKey)
                        .join(',');

                    const formData = new FormData();
                    formData.append('user_column_manager_columns', newOrder);

                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });
                }
            }
        });

        for (let i = 0; i < deleteColumnButtons.length; i++) {
            deleteColumnButtons[i].addEventListener('click', function () {
                const columnKey = this.parentElement.dataset.columnKey;

                if (confirm('Are you sure you want to delete this column?')) {
                    this.parentElement.remove();

                    // Update the columns in the backend
                    const newOrder = Array.from(customColumnsList.getElementsByClassName('custom-column-item'))
                        .map(item => item.dataset.columnKey)
                        .join(',');

                    const formData = new FormData();
                    formData.append('user_column_manager_columns', newOrder);

                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });
                }
            });
        }
    }
});