import { DynamoDBClient } from "@aws-sdk/client-dynamodb";
import {
  DynamoDBDocumentClient,
  PutCommand,
  GetCommand,
} from "@aws-sdk/lib-dynamodb";

const client = new DynamoDBClient({});
const dynamo = DynamoDBDocumentClient.from(client);

export const handler = async (event, context) => {
  let body;
  let statusCode = 200;
  const headers = {
    "Content-Type": "application/json",
  };
      body = {
        "Action": "Put an item",
    };
  
  await dynamo.send(
    new PutCommand({
        TableName: 'Events',
        Item: {
            Location: 'South Bend',
            Month: 7,
            EventName: 'Cool Summer',
            Notes: 'It is fun!'
        },
    })
 );
        
  return {
  statusCode,
  body,
  headers,
  };
};
