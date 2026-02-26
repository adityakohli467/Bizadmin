<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <?php $this->load->view('general/tailwind_common_assets'); ?>
    <style>
        .captcha-container { background: linear-gradient(135deg, #f0f2f5 0%, #e8ecf0 100%); }
    </style>
</head>
<body>
<div class="container-fluid mb-5" style="margin-top: 130px !important;">
   <div class="row">
   
    <div class="card">
    
        <h1 class="text-2xl font-bold mb-4 text-black py-6 px-6">Zouki Cafe Kitchen Production Record</h1>
       
            <form action="/Compliance/Kitchenchecklistform/save_record" method="POST"  class="bg-white p-6 rounded-lg shadow-md w-50">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date">Date</label>
                <input type="date" name="date" id="date" class="  appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="product_name">Product Name</label>
                <select name="product_name" id="product_name" class="  appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="SLICE HAM">SLICE HAM</option>
                    <option value="SLICE SALAMI">SLICE SALAMI</option>
                    <option value="SLICE PLAIN CHICKEN">SLICE PLAIN CHICKEN</option>
                    <option value="TANDOORI CHICKEN">TANDOORI CHICKEN</option>
                    <option value="SLICE TURKEY">SLICE TURKEY</option>
                    <option value="BOILED EGG'S">BOILED EGG'S</option>
                    <option value="SLICE ROAST BEEF">SLICE ROAST BEEF</option>
                    <option value="ROAST PUMPKIN">ROAST PUMPKIN</option>
                    <option value="GRILLED ZUCCHINI">GRILLED ZUCCHINI</option>
                    <option value="ROAST EGGPLANT">ROAST EGGPLANT</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="internal_batch_code_allocated">Internal Batch Code Allocated</label>
                <input type="text" name="internal_batch_code_allocated" id="internal_batch_code_allocated" class=" appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="start_slicing">Start Slicing</label>
                <input type="time" name="start_slicing" id="start_slicing" class="  appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="time_finished_slicing">Time Finished Slicing</label>
                <input type="time" name="time_finished_slicing" id="time_finished_slicing" class="  appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="temp_of_product_at_end_of_slicing">Temp of Product at End of Slicing</label>
                <input type="number" step="0.01" name="temp_of_product_at_end_of_slicing" id="temp_of_product_at_end_of_slicing" class="  appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="time_chilling_process_started">Time Chilling Process Started</label>
                <input type="time" name="time_chilling_process_started" id="time_chilling_process_started" class="  appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="time_chilling_process_finished">Time Chilling Process Finished</label>
                <input type="time" name="time_chilling_process_finished" id="time_chilling_process_finished" class="  appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="temp_of_product_at_end_of_chilling">Temp of Product at End of Chilling</label>
                <input type="number" step="0.01" name="temp_of_product_at_end_of_chilling" id="temp_of_product_at_end_of_chilling" class="  appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="comments">Comments</label>
                <textarea name="comments" id="comments" class="  appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="3"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name</label>
                <input type="text" name="name" id="name" class="  appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="signature">Signature</label>
                <input type="text" name="signature" id="signature" class="  appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Submit</button>
        </form>

        <h2 class="text-xl font-bold mt-6 mb-4">Records</h2>
        <table class="min-w-full bg-white border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border">Date</th>
                    <th class="py-2 px-4 border">Product Name</th>
                    <th class="py-2 px-4 border">Batch Code</th>
                    <th class="py-2 px-4 border">Start Slicing</th>
                    <th class="py-2 px-4 border">Time Finished Slicing</th>
                    <th class="py-2 px-4 border">Temp at End of Slicing</th>
                    <th class="py-2 px-4 border">Chilling Started</th>
                    <th class="py-2 px-4 border">Chilling Finished</th>
                    <th class="py-2 px-4 border">Temp at End of Chilling</th>
                    <th class="py-2 px-4 border">Comments</th>
                    <th class="py-2 px-4 border">Name</th>
                    <th class="py-2 px-4 border">Signature</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                    <tr>
                        <td class="py-2 px-4 border"><?php echo $record['date']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $record['product_name']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $record['internal_batch_code_allocated']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $record['start_slicing']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $record['time_finished_slicing']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $record['temp_of_product_at_end_of_slicing']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $record['time_chilling_process_started']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $record['time_chilling_process_finished']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $record['temp_of_product_at_end_of_chilling']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $record['comments']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $record['name']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $record['signature']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
</body></html>