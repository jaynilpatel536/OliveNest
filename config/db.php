<?php
/**
 * MongoDB Database Connection Configuration
 *
 * Credentials are read from environment variables so they are never
 * committed to source control. Set them in the Render dashboard:
 *
 *   MONGO_URI  = mongodb+srv://jaynil:<password>@cluster0.elrgblz.mongodb.net/
 *   MONGO_DB   = OliveNest
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Read connection settings from environment (Render injects these)
$mongoUri = getenv('MONGO_URI') ?: 'mongodb+srv://jaynil:oSNqek6cufbEpSP7@cluster0.elrgblz.mongodb.net/?retryWrites=true&w=majority';
$dbName   = getenv('MONGO_DB')  ?: 'OliveNest';

try {
    $mongoClient = new MongoDB\Client($mongoUri);
    $db = $mongoClient->$dbName;

    // Verify connection by pinging
    $mongoClient->selectDatabase('admin')->command(['ping' => 1]);

    // Ensure rooms collection has default homestays
    ensureRoomsSeeded($db);
} catch (Exception $e) {
    http_response_code(503);
    die("Database connection failed. Please try again later.");
}

/**
 * Automatically seeds initial rooms into MongoDB Atlas if collection is empty.
 */
function ensureRoomsSeeded(MongoDB\Database $db): void {
    try {
        if ($db->rooms->countDocuments([]) === 0) {
            $sampleRooms = [
                [
                    'id' => 1,
                    'title' => 'Himalayan Pine Wood Chalet',
                    'city' => 'Manali',
                    'price' => 2400,
                    'rating' => 4.9,
                    'full_address' => 'Circuit House Road, Old Manali, Himachal Pradesh',
                    'description' => 'Wake up to mist-covered pine valleys and snow peaks. Hand-crafted wooden interiors, warm fireplace, and private riverview balcony.',
                    'latitude' => 32.2432,
                    'longitude' => 77.1892,
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ],
                [
                    'id' => 2,
                    'title' => 'Goan Heritage Beach Villa',
                    'city' => 'Goa',
                    'price' => 3200,
                    'rating' => 4.8,
                    'full_address' => 'Near Calangute Beach, North Goa',
                    'description' => 'Charming Portuguese-style villa with coconut palms, private patio, hammock garden, and a 2-minute stroll to the sea.',
                    'latitude' => 15.5439,
                    'longitude' => 73.7553,
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ],
                [
                    'id' => 3,
                    'title' => 'Munnar Tea Estate Bungalow',
                    'city' => 'Munnar',
                    'price' => 1900,
                    'rating' => 4.9,
                    'full_address' => 'Mattupetty Road, Munnar, Kerala',
                    'description' => 'Colonial-style hill bungalow nestled right inside lush green tea plantations with cool mountain air and birdsong.',
                    'latitude' => 10.0889,
                    'longitude' => 77.0595,
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ],
                [
                    'id' => 4,
                    'title' => 'Kashmir Valley Traditional Nest',
                    'city' => 'Kashmir',
                    'price' => 2800,
                    'rating' => 4.9,
                    'full_address' => 'Boulevard Road, Dal Lake, Srinagar',
                    'description' => 'Authentic Kashmiri walnut woodwork, hand-woven carpets, warm kehwa tea, and breathtaking views of the Pir Panjal mountains.',
                    'latitude' => 34.0837,
                    'longitude' => 74.7973,
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ],
                [
                    'id' => 5,
                    'title' => 'Jaipur Royal Haveli Suite',
                    'city' => 'Jaipur',
                    'price' => 1500,
                    'rating' => 4.7,
                    'full_address' => 'Amber Fort Road, Amer, Jaipur, Rajasthan',
                    'description' => 'Heritage Rajasthani architecture with open courtyards, jharokha balconies, and heartwarming local hospitality.',
                    'latitude' => 26.9855,
                    'longitude' => 75.8513,
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ],
                [
                    'id' => 6,
                    'title' => 'Ganga Riverfront Spiritual Stay',
                    'city' => 'Rishikesh',
                    'price' => 1300,
                    'rating' => 4.8,
                    'full_address' => 'Tapovan, Rishikesh, Uttarakhand',
                    'description' => 'Quiet hillside retreat near the sacred Ganges with yoga terrace, fresh vegetarian kitchen, and peaceful sunsets.',
                    'latitude' => 30.1333,
                    'longitude' => 78.3242,
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ],
                [
                    'id' => 7,
                    'title' => 'Ooty Nilgiri Mist Cottage',
                    'city' => 'Ooty',
                    'price' => 1750,
                    'rating' => 4.7,
                    'full_address' => 'Fern Hill, Ooty, Tamil Nadu',
                    'description' => 'Cozy English-style brick cottage enveloped in Nilgiri mist, eucalyptus trees, and peaceful mountain walking trails.',
                    'latitude' => 11.4102,
                    'longitude' => 76.6950,
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ],
                [
                    'id' => 8,
                    'title' => 'Udaipur Lakeview Heritage Retreat',
                    'city' => 'Udaipur',
                    'price' => 2600,
                    'rating' => 4.9,
                    'full_address' => 'Lal Ghat, Lake Pichola, Udaipur, Rajasthan',
                    'description' => 'Rooftop dining with sun-drenched views over Lake Pichola and the City Palace lights shimmering on water.',
                    'latitude' => 24.5764,
                    'longitude' => 73.6835,
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ],
                [
                    'id' => 9,
                    'title' => 'Coorg Coffee Plantation Cottage',
                    'city' => 'Coorg',
                    'price' => 2100,
                    'rating' => 4.8,
                    'full_address' => 'Madikeri Rural, Kodagu, Karnataka',
                    'description' => 'Nestled inside an organic coffee and spice estate with guided nature trails and homemade Kodava delicacies.',
                    'latitude' => 12.4244,
                    'longitude' => 75.7382,
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ],
                [
                    'id' => 10,
                    'title' => 'Pondicherry French Quarter Studio',
                    'city' => 'Pondicherry',
                    'price' => 1800,
                    'rating' => 4.6,
                    'full_address' => 'Rue Suffren, White Town, Puducherry',
                    'description' => 'Classic French louvred shutters, bougainvillea blooms, pastel streets, and a 3-minute walk to the beach promenade.',
                    'latitude' => 11.9338,
                    'longitude' => 79.8333,
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ],
                [
                    'id' => 11,
                    'title' => 'Shillong Cloud Pine Homestay',
                    'city' => 'Shillong',
                    'price' => 2000,
                    'rating' => 4.9,
                    'full_address' => 'Upper Shillong, Meghalaya',
                    'description' => 'Experience the Scotland of the East with pine-shaded gardens, live acoustic music, and crystal-clear waterfalls nearby.',
                    'latitude' => 25.5788,
                    'longitude' => 91.8933,
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ]
            ];

            $db->rooms->insertMany($sampleRooms);

            $db->counters->updateOne(
                ['_id' => 'rooms'],
                ['$set' => ['seq' => 11]],
                ['upsert' => true]
            );
        }
    } catch (Exception $e) {
        // Silently fail if seeding encounters a temporary race condition
    }
}

/**
 * Generate auto-increment numeric IDs for collections.
 * Uses a 'counters' collection to track the next ID for each collection.
 */
function getNextId(MongoDB\Database $db, string $collectionName): int {
    $result = $db->counters->findOneAndUpdate(
        ['_id' => $collectionName],
        ['$inc' => ['seq' => 1]],
        [
            'upsert'         => true,
            'returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
        ]
    );
    return (int) $result['seq'];
}

/**
 * Convert a MongoDB BSON document to a plain PHP associative array.
 * Handles nested documents, arrays, ObjectIds, and UTCDateTime.
 */
function docToArray($doc) {
    if ($doc instanceof MongoDB\Model\BSONDocument || $doc instanceof MongoDB\Model\BSONArray) {
        return array_map('docToArray', (array) $doc);
    }
    if ($doc instanceof MongoDB\BSON\ObjectId) {
        return (string) $doc;
    }
    if ($doc instanceof MongoDB\BSON\UTCDateTime) {
        return $doc->toDateTime()->format('Y-m-d H:i:s');
    }
    return $doc;
}
