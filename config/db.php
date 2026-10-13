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
} catch (Exception $e) {
    http_response_code(503);
    die("Database connection failed. Please try again later.");
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
